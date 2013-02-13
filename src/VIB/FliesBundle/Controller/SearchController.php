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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

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
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function searchAction() {
        
        $em = $this->getEntityManager();
        
        $form = $this->getFormFactory()->create(new SearchType());
        
        return array(
            'searchForm' => $form->createView());
    }
    
    /**
     * Handle search result
     * 
     * @Route("/search/result/", name="searchResult")
     * @Route("/search/result/page/{page}", name="searchResultPage")
     * @Template()
     * 
     * @param string $term
     * @param string $filter
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function searchResultAction($page = 1) {
        
        $em = $this->getEntityManager();
        
        $form = $this->getFormFactory()->create(new SearchType());
        $request = $this->getRequest();
        $session = $request->getSession();
        
        $stocks = null;
        $crosses = null;
        $vials = null;
        $pager = null;
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $data = $form->getData();
                $term = $data['term'];
                $filter = $data['filter'];
                
                $session->set('search_term',$term);
                $session->set('search_filter',$filter);
            }
        } else {          
            $term = $session->get('search_term');
            $filter = $session->get('search_filter');
        }
        
        switch($filter) {
            case 'stocks':
                $queryBuilder = $this->getEntityManager()
                    ->getRepository('VIBFliesBundle:FlyStock')
                    ->findStocksByName($term);
                $adapter = new DoctrineORMAdapter($queryBuilder);
                $pager = new Pagerfanta($adapter);
                $pager->setMaxPerPage(15);
                $pager->setCurrentPage($page);
                $stocks = $pager->getCurrentPageResults();
                break;
            case 'crosses':
                $queryBuilder = $this->getEntityManager()
                    ->getRepository('VIBFliesBundle:FlyCross')
                    ->findLivingCrossesByName($term);
                $adapter = new DoctrineORMAdapter($queryBuilder);
                $pager = new Pagerfanta($adapter);
                $pager->setMaxPerPage(15);
                $pager->setCurrentPage($page);
                $crosses = $pager->getCurrentPageResults();
                break;
            case 'stock vials':
                $queryBuilder = $this->getEntityManager()
                    ->getRepository('VIBFliesBundle:FlyVial')
                    ->findLivingStocksByName($term);
                $adapter = new DoctrineORMAdapter($queryBuilder);
                $pager = new Pagerfanta($adapter);
                $pager->setMaxPerPage(15);
                $pager->setCurrentPage($page);
                $vials = $pager->getCurrentPageResults();
                break;
            default:
                break;
        }

        return array('stocks' => $stocks,
                     'crosses' => $crosses,
                     'vials' => $vials,
                     'pager' => $pager,
                     'filter' => $filter,
                     'term' => $term,
                     'form' => $form->createView());
    }
}

?>
