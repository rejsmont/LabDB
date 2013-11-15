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

use VIB\SearchBundle\Search\SearchQuery;
use VIB\SearchBundle\Search\SearchQueryInterface;

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
        $form = $this->createForm($this->createAdvancedSearchForm());
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
        $form = $this->createForm($this->createSearchForm());
        return array(
            'form' => $form->createView(),
            'realm' => $this->getSearchRealm()
        );
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
        $form = $this->createForm($this->createSearchForm(), $this->createSearchQuery());
        $advancedForm = $this->createForm($this->createAdvancedSearchForm(), $this->createSearchQuery(true));
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bind($request);
            $advancedForm->bind($request);
            
            if ($form->isValid()) {
                $searchQuery = $form->getData();
            } elseif ($advancedForm->isValid()) {
                $searchQuery = $form->getData();
            }
            $this->saveSearchQuery($searchQuery);
            
        } else {
            $searchQuery = $this->loadSearchQuery();
        }

        $repository = $this->getObjectManager()->getRepository($searchQuery->getEntityClass());
        
        if ($repository instanceof SearchableRepositoryInterface) {
            return $this->handleSearchableRepository($repository, $searchQuery);
        } else {
            return $this->handleNonSearchableRepository($repository, $searchQuery);
        }
        
        throw $this->createNotFoundException();
    }
    
    /**
     * Load search query from session
     * 
     * @return SearchQuery
     */
    protected function loadSearchQuery()
    {
        $serializer = $this->get('serializer');
        $serializedQuery = $this->getSession()->get(
                $this->getSearchRealm() . '_search_query', 
                $this->createSearchQuery()
        );
        
        return $serializer->deserialize($serializedQuery['object'], $serializedQuery['class'], 'json');
    }
    
    /**
     * Save search query in session
     * 
     * @param type $searchQuery
     */
    protected function saveSearchQuery($searchQuery)
    {
        $serializer = $this->get('serializer');
        $serializedQuery = array(
            'object' => $serializer->serialize($searchQuery, 'json'),
            'class' => get_class($searchQuery)
        );
        $this->getSession()->set($this->getSearchRealm() . '_search_query', $serializedQuery);
    }
    
    /**
     * Handle non-searchable repository classes
     * 
     * @param mixed $repository
     * @param mixed $searchQuery
     * @return mixed
     */
    protected function handleNonSearchableRepository($repository, $searchQuery)
    {
        throw $this->createNotFoundException();
    }
    
    /**
     * Handle searchable repository classes
     * 
     * @param mixed $repository
     * @param mixed $searchQuery
     * @return mixed
     */    
    protected function handleSearchableRepository($repository, $searchQuery)
    {        
        $terms = $searchQuery->getTerms();
        $excluded = $searchQuery->getExcluded();
        $options = $searchQuery->getOptions();
        
        if (count($terms) == 0) {
            $this->createNotFoundException();
        }
        
        $resultCount = $repository->getSearchResultCount($terms, $excluded, $options);
        $result = $repository->getSearchQuery($terms, $excluded, $options)
                             ->setHint('knp_paginator.count', $resultCount);
        $paginator  = $this->getPaginator();
        $page = $this->getCurrentPage();
        $entities = $paginator->paginate($result, $page, 25);

        return array('entities' => $entities,
                     'query' => implode(' ', $terms),
                     'exclude' => implode(' ', $excluded),
                     'options' => $options);
    }
    
    /**
     * Get search Query
     * 
     * @param boolean $advanced
     * @return \VIB\SearchBundle\Search\SearchQueryInterface
     */
    abstract protected function createSearchQuery($advanced = false);
    
    /**
     * Get search form
     * 
     * @return \VIB\SearchBundle\Form\SearchType
     */
    protected function createSearchForm()
    {
        return new SearchType();
    }
    
    /**
     * Get advanced search form
     * 
     * @return \VIB\SearchBundle\Form\AdvancedSearchType
     */
    protected function createAdvancedSearchForm()
    {
        return new AdvancedSearchType();
    }
    
    /**
     * Get search realm
     * 
     * @return string
     */
    abstract protected function getSearchRealm();
}
