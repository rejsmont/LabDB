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

namespace VIB\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VIB\CoreBundle\Controller\AbstractController;
use VIB\CoreBundle\Repository\SearchableRepositoryInterface;

/**
 * SearchController
 *
 * @Route("/search")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends AbstractController
{
    /**
     * Handle search request
     *
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function searchAction()
    {
        return $this->render('VIBCoreBundle:Search:search.html.twig');
    }
    
    /**
     * Handle advanced search request
     *
     * @Template()
     * @Route("/") 
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function advancedAction()
    {
        $form = $this->createForm(new AdvancedSearchType());
        return array('form' => $form->createView());
    }

    /**
     * Render search form
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function formAction()
    {
        $form = $this->createForm(new SearchType());
        $msie_version = 10;
        $request = $this->get('request');
        $user_agent = $request->headers->get('User-Agent');
        $is_msie = strpos($user_agent, 'MSIE') !== false;
        if ($is_msie) {
            $matches = array();
            preg_match('/MSIE (.*?)\./', $user_agent, $matches);
            $msie_version = array_key_exists(1, $matches) ? $matches[1] : 0;
            if ($msie_version < 8) {
                $msie = true;
            } else {
                $msie = false;
            }
        } else {
            $msie = false;
        }

        return $this->render('VIBCoreBundle:Search:form.html.twig', array(
            'form' => $form->createView(),
            'msie' => $msie ));
    }

    /**
     * Handle search result
     *
     * @Route("/result/")
     * @Template()
     *
     * @return array
     */
    public function resultAction()
    {
        $om = $this->getObjectManager();
        $form = $this->createForm(new SearchType());
        $advForm = $this->createForm(new AdvancedSearchType());
        $request = $this->get('request');
        $session = $request->getSession();
        $securityContext = $this->getSecurityContext();

        if ($request->getMethod() == 'POST') {

            $realm = $this->getSearchRealm();

            $form->bind($request);
            $advForm->bind($request);
            
            if ($form->isValid()) {
                $data = $form->getData();
                $advanced = false;
            } elseif ($advForm->isValid()) {
                $data = $advForm->getData();
                $advanced = true;
            } else {
                $data = false;
            }
            
            if (false !== $data) {
                $term = $data['query'];
                if ('' == $data['filter']) {
                    $filter = $this->getDefaultFilter();
                } else {
                    $filter = $data['filter'];
                }
                $exclude = $advanced ? $data['exclude'] : '';
                $options = $advanced ? $data['options'] : array();
                $session->set($realm . '_search_query',$term);
                $session->set($realm . '_search_exclude',$exclude);
                $session->set($realm . '_search_filter',$filter);
                $session->set($realm . '_search_options',$options);
            }
        } else {
            $term = $session->get($realm . '_search_query');
            $exclude = $session->get($realm . '_search_exclude');
            $filter = $session->get($realm . '_search_filter');
            $options = $session->get($realm . '_search_options');
        }
        
        $repository = $om->getRepository($this->filterToClass($filter));
        
        if ($repository instanceof SearchableRepositoryInterface) {
            
            $terms = $term != '' ? explode(' ',$term) : array();
            $excluded = $exclude != '' ? explode(' ',$exclude) : array();
            
            $resultCount = $repository->getSearchResultCount($terms, $excluded, $options, $securityContext);
            $result = $repository->getSearchQuery($terms, $excluded, $options, $securityContext)
                                 ->setHint('knp_paginator.count', $resultCount);
            $paginator  = $this->getPaginator();
            $page = $this->getCurrentPage();
            $entities = $paginator->paginate($result, $page, 25);

            return array('entities' => $entities,
                         'query' => $term,
                         'exclude' => $exclude,
                         'filter' => $filter,
                         'options' => $options);
        }

        return $this->createNotFoundException();
    }
    
    /**
     * Get default filter
     * 
     * @return string
     */
    protected function getDefaultFilter()
    {
        return 'entity';
    }
    
    /**
     * Get search realm
     * 
     * @return string
     */
    protected function getSearchRealm()
    {
        return 'core';
    }
    
    /**
     * Get class lookup table
     * 
     * @return array
     */
    protected function getClassLookupTable()
    {
        return array('entity' => 'VIB\CoreBundle\Entity\Entity');
    }
    
    /**
     * Convert filter string to class name
     * 
     * @param string $filter
     * @return string
     */
    protected function filterToClass($filter)
    {
        $lookup = $this->getClassLookupTable();
        
        return key_exists($filter, $lookup) ? $lookup[$filter] : '';
    }
}
