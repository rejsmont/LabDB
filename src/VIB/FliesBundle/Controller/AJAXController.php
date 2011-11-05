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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use VIB\FliesBundle\Entity\FlyVial;

/**
 * Description of AJAXController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends Controller {
    
    /**
     * Handle vial AJAX request
     * 
     * @param integer $id
     * @return mixed
     * 
     * @Route("/ajax/vials/{id}.{format}", name="ajax_vial_json")
     * @Route("/ajax/vials/{id}", name="ajax_vial")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */    
    public function vialAction($id,$format = null) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        
        $serializer = $this->get('serializer');
        
        
        if ($format == 'json') {
            return new Response($serializer->serialize($vial, 'json')); 
        }
        
        return array('vial' => $vial);;
    }
}

?>
