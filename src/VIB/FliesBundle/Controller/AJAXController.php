<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\StockVial;
use VIB\FliesBundle\Entity\CrossVial;
use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\Rack;
use VIB\FliesBundle\Entity\RackPosition;

/**
 * Description of AJAXController
 *
 * @Route("/ajax")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends Controller {
    
    /**
     * Handle vial AJAX request
     * 
     * @Route("/vials")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function vialAction(Request $request) {
        
        $id = $request->query->get('id');
        $filter = $request->query->get('filter');
        $format = $request->query->get('format');
        $order = $request->query->get('order',null);
        
        $em = $this->get('doctrine.orm.entity_manager');
        $securityContext = $this->get('security.context');
        $vial = $em->find('VIBFliesBundle:Vial', $id);
        $type = $filter !== null ? ' ' . $filter : '';
        
        if((! $vial instanceof Vial)||(($filter !== null)&&($vial->getType() != $filter))) {
            return new Response('The' . $type . ' vial ' . sprintf("%06d",$id) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to' . $type . ' vial ' . sprintf("%06d",$id) . ' denied', 401);
        }
        
        $serializer = $this->get('serializer');
        
        if ($format == 'json') {
            return new Response($serializer->serialize($vial, 'json')); 
        } else {
            return array('entity' => $vial, 'checked' => 'checked', 'type' => $filter, 'order' => $order);
        }
    }
    
    /**
     * Handle rack vial AJAX request
     * 
     * @Route("/racks/vials")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function rackVialAction(Request $request) {
        
        $vialID = $request->query->get('vialID');
        $positionID = $request->query->get('positionID');
        $rackID = $request->query->get('rackID');
        $order = $request->query->get('order',null);
        
        $em = $this->get('doctrine.orm.entity_manager');
        $securityContext = $this->get('security.context');
        $vial = $em->find('VIBFliesBundle:Vial', $vialID);
        $position = $em->find('VIBFliesBundle:RackPosition', $positionID);
        
        if(($vialID != null)&&(! $vial instanceof Vial)) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to vial ' . sprintf("%06d",$vialID) . ' denied', 401);
        }
        
        if(! $position instanceof RackPosition) {
            return new Response('Selected position does not exist', 404);
        } elseif (($vialID != null)&&(! $position->isEmpty())) {
            return new Response('Selected position is not empty', 406);
        }
        
        $vial->setPosition($position);
        $em->persist($vial);
        $em->flush();
        
        return array('contents' => $vial, 'rackID' => $rackID, 'order' => $order);
    }
    
    /**
     * Handle rack vial AJAX request
     * 
     * @Route("/racks/vials/remove")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function rackVialRemoveAction(Request $request) {
        
        $vialID = $request->query->get('vialID');
        $rackID = $request->query->get('rackID');
        
        $em = $this->get('doctrine.orm.entity_manager');
        $securityContext = $this->get('security.context');
        $vial = (null !== $vialID) ? $em->find('VIBFliesBundle:Vial', $vialID) : null;
        $rack = (null !== $rackID) ? $em->find('VIBFliesBundle:Rack', $rackID) : null;
        
        if(($vialID != null)&&(! $vial instanceof Vial)) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to' . $type . ' vial ' . sprintf("%06d",$id) . ' denied', 401);
        }
        
        if(($rackID != null)&&(! $rack instanceof Rack)) {
            return new Response('The rack R'. sprintf("%06d",$rackID) . ' does not exist', 404);
        } elseif (($vialID != null)&&(! $rack->hasVial($vial))) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' is not in the rack R'. sprintf("%06d",$rackID), 404);
        }
        
        if ($vialID !== null) { 
            $vial->setPosition(null);
            $em->persist($vial);
            $em->flush();
            return new Response('The vial'. sprintf("%06d",$rackID) . ' was removed from rack R'. sprintf("%06d",$rackID), 200);
        } else {
            $rack->clearVials();
            $em->persist($rack);
            $em->flush();
            return new Response('The rack R'. sprintf("%06d",$rackID) . ' was cleared', 200);
        }
    }
    
    /**
     * Handle stock search AJAX request
     * 
     * @Route("/stocks/search")
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function stockSearchAction(Request $request) {
        
        $query = $request->query->get('query');
        $qb = $this->getDoctrine()
                   ->getRepository('VIBFliesBundle:Stock')
                   ->search($query);
        $found = $this->get('vib.security.helper.acl')
                      ->apply($qb)
                      ->getResult();
        
        $stockNames = array();
        
        foreach ($found as $stock) {
            $stockNames[] = $stock->getName();
        }
        
        $response = new JsonResponse();
        $response->setData(array('options' => $stockNames));
        
        return $response;
    }
    
    /**
     * Handle stock search AJAX request
     * 
     * @Route("/popover")
     * @Template()
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function popoverAction(Request $request) {
        $type = $request->query->get('type');
        $id = $request->query->get('id');
        $rack = $request->query->get('rack');
        
        switch($type) {
            case 'vial':
                $entity =  $this->getDoctrine()
                                ->getRepository('VIBFliesBundle:Vial')
                                ->find($id);
                $etype = "Vial";
                break;
            case 'stock':
                $entity =  $this->getDoctrine()
                                ->getRepository('VIBFliesBundle:Stock')
                                ->find($id);
                $etype = "Stock";
                break;
            default:
                return new Response('Unrecognized type', 404);
        }
        
        $status = '<div class="status">';
        
        if ($entity instanceof Vial) {
            if($entity->isTrashed()) {
                $status .= '<span title="trashed" class="label status label-inverse"><i class="icon-trash"></i></span>';
            } elseif($entity->isAlive()) {
                $status .= '<span title="alive" class="label status label-success"><i class="icon-heart"></i></span>';
            } else {
                $status .= '<span title="dead" class="label status label-important"><i class="icon-remove-sign"></i></span>';
            }
            if ($entity->getTemperature() < 21) {
                $status .= '<span class="label status label-info">' . $entity->getTemperature() . '℃</span>';
            } elseif ($entity->getTemperature() < 25) {
                $status .= '<span class="label status label-success">' . $entity->getTemperature() . '℃</span>';
            } elseif ($entity->getTemperature() < 28) {
                $status .= '<span class="label status label-warning">' . $entity->getTemperature() . '℃</span>';
            } else {
                $status .= '<span class="label status label-important">' . $entity->getTemperature() . '℃</span>';
            }
            if ($entity instanceof CrossVial) {
                if ($entity->isSuccessful()) {
                    $status .= '<span title="successful" class="label status label-success"><i class="icon-ok"></i></span>';
                } elseif ($entity->isSterile()) {
                    $status .= '<span title="sterile" class="label status label-important"><i class="icon-remove-circle"></i></span>';
                } elseif (null !== $entity->isSuccessful()) {
                    $status .= '<span title="failed" class="label status label-warning"><i class="icon-remove"></i></span>';
                }
                    
                $type  = "crossvial";
                $etype = "Cross";
            } elseif (($entity instanceof StockVial)&&(null !== $entity->getStock())) {
                $type  = "stockvial";
            }            
        } elseif ($entity instanceof Stock) {
            $vials = count($entity->getLivingVials());
            if($vials > 3) {
                $status .= '<span title="amplified" class="label status label-success"><i class="icon-plus-sign"></i></span>';
            } elseif($vials > 1) {
                $status .= '<span title="healthy" class="label status label-success"><i class="icon-ok-sign"></i></span>';
            } elseif($vials < 1) {
                $status .= '<span title="dead" class="label status label-important"><i class="icon-remove-sign"></i></span>';
            } else {
                $status .= '<span title="expand" class="label status label-warning"><i class="icon-minus-sign"></i></span>';
            }
        } else {
             return new Response('Not found', 404);
        }
        
        $status .= '</div>';
        
        $owner = $this->getOwner($entity);

        $html = $this->render('VIBFliesBundle:AJAX:popover.html.twig',
                array('type' => $type, 'entity' => $entity, 'owner' => $owner, 'rack' => $rack))->getContent();
        $title = "<b>" . $etype . " " . $entity . "</b>" . $status;
        
        $response = new JsonResponse();
        $response->setData(array('title' => $title, 'html' => $html));
        
        return $response;
    }
    
    /**
     * Get owner of entity
     * 
     * @param object $entity
     */
    protected function getOwner($entity) {
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $aclProvider = $this->get('security.acl.provider');
        $acl = $aclProvider->findAcl($objectIdentity);
        foreach($acl->getObjectAces() as $ace) {
            if ($ace->getMask() == MaskBuilder::MASK_OWNER) {
                $securityIdentity = $ace->getSecurityIdentity();
                if ($securityIdentity instanceof UserSecurityIdentity) {
                    $userManager = $this->get('fos_user.user_manager');
                    return $userManager->findUserByUsername($securityIdentity->getUsername());
                }
            }
        }
        return null;
    }
}

?>
