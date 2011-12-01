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

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Entity\FlyCross;

use VIB\FliesBundle\Form\SearchType;


/**
 * Description of SearchController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends AbstractController {
    
    /**
     * Handle search request
     *
     * @Template()
     *  
     * @param string $term
     * @param string $filter
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function searchAction($term = null, $filter = null) {
        
        $em = $this->getEntityManager();
        
        $form = $this->getFormFactory()->create(new SearchType());
        
        return array(
            'form' => $form->createView());
    }
    
    /**
     * Handle search result
     * 
     * @Route("/search/", name="searchResult")
     * @Template()
     * 
     * @param string $term
     * @param string $filter
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function searchResultAction($term = null, $filter = null) {
        
        $em = $this->getEntityManager();
        
        $form = $this->getFormFactory()->create(new SearchType());
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $term = $data['term'];
                $filter = $data['filter'];
                
                switch($filter) {
                    case 's':
                    case 'c':
                    default:
                        break;
                }

                return $this->render('VIBFliesBundle:Search:searchResult.html.twig');
            }
        }        
    }
}

?>
