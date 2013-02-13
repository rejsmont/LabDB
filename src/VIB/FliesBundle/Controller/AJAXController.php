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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Entity\FlyCross;


/**
 * Description of AJAXController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends Controller {
    
    /**
     * Handle vial AJAX request
     * 
     * @Route("/ajax/vials/{id}.{format}", name="ajax_vial_format")
     * @Route("/ajax/vials/{filter}/{id}.{format}", name="ajax_vial_filter_format")
     * @Route("/ajax/vials/{id}/", name="ajax_vial")
     * @Route("/ajax/vials/{filter}/{id}/", name="ajax_vial_filter")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function vialAction($id, $filter = null, $format = null) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        
        if(! $vial) {
            return new Response('The vial ' . sprintf("%06d",$id) . ' does not exist', 404);
        }
        
        $serializer = $this->get('serializer');
        
        if ($filter == 'cross') {
            
            $cross = $vial->getCross();
            
            if (! $cross) {
                return new Response('The vial ' . sprintf("%06d",$id) . ' is not a cross vial', 404);
            } else {
                if ($format == 'json') {
                    return new Response($serializer->serialize($vial, 'json')); 
                } else {
                    return array('cross' => $cross,
                                 'vial' => null);
                }
            }
        }
        
        if (($filter == 'stock')&&(! $vial->getStock())) {
            return new Response('The vial ' . sprintf("%06d",$id) . ' is not a stock vial', 404);
        }
        
        if ($format == 'json') {
            return new Response($serializer->serialize($vial, 'json')); 
        } else {
            return array('vial' => $vial,
                         'cross' => null);
        }
    }
    
    /**
     * Handle stock search AJAX request
     * 
     * @Route("/ajax/stocks/search", name="ajax_stock_search")
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function stockSearchAction(Request $request) {
        
        $query = $request->query->get('query');
        $found = $this->getDoctrine()
                      ->getRepository('VIBFliesBundle:FlyStock')
                      ->findStocksByName($query)
                      ->getQuery()
                      ->getResult();
        
        $stockNames = array();
        
        foreach ($found as $stock) {
            $stockNames[] = $stock->getName();
        }
        
        $response = new JsonResponse();
        $response->setData(array('options' => $stockNames));
        
        return $response;
    }
}

?>
