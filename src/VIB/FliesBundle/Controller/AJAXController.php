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

use VIB\FliesBundle\Entity\Vial;

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
     * @Route("/vials/{id}.{format}", defaults={"filter" = null, "format" = "json"})
     * @Route("/vials/{filter}/{id}.{format}", defaults={"filter" = null, "format" = "json"})
     * @Template()
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function vialAction($id, $filter = null, $format = null) {
        
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
            return array('entity' => $vial, 'checked' => 'checked');
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
                   ->findStocksByName($query);
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
        $html = $this->render('VIBFliesBundle:AJAX:popover.html.twig',
                array('type' => $type, 'id' => $id))->getContent();
        $title = "Cross " . $id;
        
        $response = new JsonResponse();
        $response->setData(array('title' => $title, 'html' => $html));
        
        return $response;
    }
}

?>
