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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\NoResultException;

use VIB\CoreBundle\Controller\AbstractController;
use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\StockVial;
use VIB\FliesBundle\Entity\CrossVial;
use VIB\FliesBundle\Entity\InjectionVial;
use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\Rack;
use VIB\FliesBundle\Entity\RackPosition;

/**
 * Description of AJAXController
 *
 * @Route("/_ajax")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends AbstractController
{
    /**
     * Handle vial AJAX request
     *
     * @Route("/vials")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function vialAction(Request $request)
    {
        $id = $request->query->get('id');
        $filter = $request->query->get('filter', null);
        $format = $request->query->get('format', null);
        $order = $request->query->get('order', null);

        $om = $this->getObjectManager();
        $securityContext = $this->getSecurityContext();
        try {
            $vial = $om->find('VIBFliesBundle:Vial', $id);
        } catch (NoResultException $e) {
            $vial = null;
        }
        $type = $filter !== null ? ' ' . $filter : '';

        if ((! $vial instanceof Vial)||(($filter !== null)&&($vial->getType() != $filter))) {
            return new Response('The' . $type . ' vial ' . sprintf("%06d",$id) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to' . $type . ' vial ' . sprintf("%06d",$id) . ' denied', 401);
        }

        $serializer = $this->get('serializer');

        if ($format == 'json') {
            return new JsonResponse($serializer->serialize($vial, 'json'));
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
    public function rackVialAction(Request $request)
    {
        $vialID = $request->query->get('vialID');
        $positionID = $request->query->get('positionID');
        $rackID = $request->query->get('rackID');
        $order = $request->query->get('order',null);

        $om = $this->getObjectManager();
        $securityContext = $this->getSecurityContext();
        try {
            $vial = $om->find('VIBFliesBundle:Vial', $vialID);
        } catch (NoResultException $e) {
            $vial = null;
        }
        try {
            $position = $om->find('VIBFliesBundle:RackPosition', $positionID);
        } catch (NoResultException $e) {
            $position = null;
        }

        if (! $vial instanceof Vial) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to vial ' . sprintf("%06d",$vialID) . ' denied', 401);
        }

        if (! $position instanceof RackPosition) {
            return new Response('Selected position does not exist', 404);
        } elseif (($vialID != null)&&(! $position->isEmpty())) {
            return new Response('Selected position is not empty', 406);
        }

        $vial->setPosition($position);
        $om->persist($vial);
        $om->flush();

        return array('content' => $vial, 'rackID' => $rackID, 'order' => $order);
    }

    /**
     * Handle rack vial AJAX request
     *
     * @Route("/racks/vials/remove")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function rackVialRemoveAction(Request $request)
    {
        $vialID = $request->query->get('vialID');
        $rackID = $request->query->get('rackID');

        $om = $this->getObjectManager();
        $securityContext = $this->getSecurityContext();

        try {
            $vial = (null !== $vialID) ? $om->find('VIBFliesBundle:Vial', $vialID) : null;
        } catch (NoResultException $e) {
            $vial = null;
        }
        try {
            $rack = (null !== $rackID) ? $om->find('VIBFliesBundle:Rack', $rackID) : null;
        } catch (NoResultException $e) {
            $rack = null;
        }

        if ((null !== $vialID)&&(! $vial instanceof Vial)) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' does not exist', 404);
        } elseif (!($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('VIEW', $vial))) {
            return new Response('Access to vial ' . sprintf("%06d",$vialID) . ' denied', 401);
        }

        if ((null === $vialID)&&(! $rack instanceof Rack)) {
            return new Response('The rack R'. sprintf("%06d",$rackID) . ' does not exist', 404);
        } elseif (($vialID != null)&&(! $rack->hasContent($vial))) {
            return new Response('The vial ' . sprintf("%06d",$vialID) . ' is not in the rack R'. sprintf("%06d",$rackID), 404);
        }

        if ($vialID !== null) {
            $vial->setPosition(null);
            $om->persist($vial);
            $om->flush();

            return new Response('The vial '. sprintf("%06d",$rackID) . ' was removed from rack R'. sprintf("%06d",$rackID), 200);
        } else {
            $rack->clearContents();
            $om->persist($rack);
            $om->flush();

            return new Response('The rack R'. sprintf("%06d",$rackID) . ' was cleared', 200);
        }
    }

    /**
     * Handle stock search AJAX request
     *
     * @Route("/stocks/search")
     *
     * @param  Symfony\Component\HttpFoundation\Request  $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function stockSearchAction(Request $request)
    {
        $terms = explode(' ',$request->query->get('query'));
        $query = $this->getObjectManager()->getRepository('VIBFliesBundle:Stock')->getSearchQuery($terms);
        $found = $query->getResult();

        $stockNames = array();
        foreach ($found as $stock) {
            $stockNames[] = $stock->getName();
        }

        $response = new JsonResponse();
        $response->setData(array('options' => $stockNames));

        return $response;
    }

    /**
     * Handle popover AJAX request
     *
     * @Route("/popover")
     * @Template()
     *
     * @param  Symfony\Component\HttpFoundation\Request  $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function popoverAction(Request $request)
    {
        $type = $request->query->get('type');
        $id = $request->query->get('id');
        $rack = $request->query->get('rack');
        $om = $this->getObjectManager();

        try {
            switch ($type) {
                case 'vial':
                    $entity =  $om->getRepository('VIBFliesBundle:Vial')->find($id);
                    $etype = "Vial";
                    break;
                case 'stock':
                    $entity =  $om->getRepository('VIBFliesBundle:Stock')->find($id);
                    $etype = "Stock";
                    break;
                default:
                    return new Response('Unrecognized type', 406);
            }
        } catch (NoResultException $e) {
            $entity = null;
        }

        $status = '<div class="status">';
        if ($entity instanceof Vial) {
            if ($entity->isTrashed()) {
                $status .= '<span title="trashed" class="label status label-default"><i class="fa fa-trash-o"></i></span>';
            } elseif ($entity->isAlive()) {
                $status .= '<span title="alive" class="label status label-success"><i class="fa fa-heart"></i></span>';
            } else {
                $status .= '<span title="dead" class="label status label-danger"><i class="fa fa-times-circle"></i></span>';
            }
            if ($entity->getTemperature() < 21) {
                $status .= '<span class="label status label-info">' . $entity->getTemperature() . '℃</span>';
            } elseif ($entity->getTemperature() < 25) {
                $status .= '<span class="label status label-success">' . $entity->getTemperature() . '℃</span>';
            } elseif ($entity->getTemperature() < 28) {
                $status .= '<span class="label status label-warning">' . $entity->getTemperature() . '℃</span>';
            } else {
                $status .= '<span class="label status label-danger">' . $entity->getTemperature() . '℃</span>';
            }
            if ($entity instanceof CrossVial) {
                if ($entity->isSuccessful()) {
                    $status .= '<span title="successful" class="label status label-success"><i class="fa fa-ok"></i></span>';
                } elseif ($entity->isSterile()) {
                    $status .= '<span title="sterile" class="label status label-important"><i class="fa fa-times-circle"></i></span>';
                } elseif (null !== $entity->isSuccessful()) {
                    $status .= '<span title="failed" class="label status label-warning"><i class="fa fa-times"></i></span>';
                }

                $type  = "crossvial";
                $etype = "Cross";
            } elseif (($entity instanceof StockVial)&&(null !== $entity->getStock())) {
                $type  = "stockvial";
            } elseif (($entity instanceof InjectionVial)&&(null !== $entity->getTargetStock())) {
                $type  = "injectionvial";
                $etype = "Injection";
            }
        } elseif ($entity instanceof Stock) {
            $vials = count($entity->getLivingVials());
            if ($vials > 3) {
                $status .= '<span title="amplified" class="label status label-success"><i class="fa fa-plus-circle"></i></span>';
            } elseif ($vials > 1) {
                $status .= '<span title="healthy" class="label status label-success"><i class="fa fa-check-circle"></i></span>';
            } elseif ($vials < 1) {
                $status .= '<span title="dead" class="label status label-danger"><i class="fa fa-times-circle"></i></span>';
            } else {
                $status .= '<span title="expand" class="label status label-warning"><i class="fa fa-minus-circle"></i></span>';
            }
        } else {
             return new Response('Not found', 404);
        }
        $status .= '</div>';
        $owner = $om->getOwner($entity);
        $html = $this->render('VIBFliesBundle:AJAX:popover.html.twig',
                array('type' => $type, 'entity' => $entity, 'owner' => $owner, 'rack' => $rack))->getContent();
        $title = "<b>" . $etype . " " . $entity . "</b>" . $status;

        $response = new JsonResponse();
        $response->setData(array('title' => $title, 'html' => $html));

        return $response;
    }
}
