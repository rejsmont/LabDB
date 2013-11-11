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

namespace VIB\SearchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VIB\CoreBundle\Controller\AbstractController;
use VIB\SearchBundle\Repository\SearchableRepositoryInterface;

use VIB\SearchBundle\Form\SearchType;
use VIB\SearchBundle\Form\AdvancedSearchType;

/**
 * SearchController
 *
 * @Route("/search")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class SearchController extends AbstractController
{
    /**
     * Render advanced search form
     *
     * @Template()
     * @Route("/") 
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function advancedAction()
    {
        $form = $this->createForm(new AdvancedSearchType());
        return array(
            'form' => $form->createView(),
            'realm' => $this->getSearchRealm()
        );
    }

    /**
     * Render quick search form
     *
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function searchAction()
    {
        $form = $this->createForm(new SearchType());
        return array(
            'form' => $form->createView(),
            'realm' => $this->getSearchRealm() );
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
                    $filter = $this->getDefaultFilter($term);
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
            return $this->handleSearchableRepository($repository, $term, $filter, $exclude, $options);
        } else {
            return $this->handleNonSearchableRepository($repository, $term, $filter, $exclude, $options);
        }
    }
    
    /**
     * Handle non-searchable repository classes
     * 
     * @param string $term
     * @param string $filter
     * @param string $excluded
     * @param array $options
     * @return mixed
     */
    protected function handleNonSearchableRepository($repository, $term, $filter, $exclude = '', $options = array())
    {
        return $this->createNotFoundException();
    }
    
    /**
     * Handle searchable repository classes
     * 
     * @param string $term
     * @param string $filter
     * @param string $excluded
     * @param array $options
     * @return mixed
     */    
    protected function handleSearchableRepository($repository, $term, $filter, $exclude = '', $options = array())
    {
        if (trim($term) == '') {
            return $this->createNotFoundException();
        }
        
        $securityContext = $this->getSecurityContext();
        
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
    
    /**
     * Get default filter
     * 
     * @param string $term
     * @return string
     */
    abstract protected function getDefaultFilter($term = '');
    
    /**
     * Get search realm
     * 
     * @return string
     */
    abstract protected function getSearchRealm();
    
    /**
     * Get class lookup table
     * 
     * @return array
     */
    abstract protected function getClassLookupTable();
}
