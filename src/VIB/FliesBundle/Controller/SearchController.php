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

use VIB\SearchBundle\Controller\SearchController as BaseSearchController;

use VIB\FliesBundle\Search\SearchQuery;

use VIB\FliesBundle\Form\SearchType;
use VIB\FliesBundle\Form\AdvancedSearchType;

/**
 * Search controller for the flies bundle
 *
 * @Route("/search")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends BaseSearchController
{
    /**
     * {@inheritdoc}
     */
    protected function handleSearchableRepository($repository, $searchQuery)
    {        
        if (! $searchQuery instanceof SearchQuery) {
            throw new \InvalidArgumentException();
        }
        
        $output = array_merge(
                parent::handleSearchableRepository($repository, $searchQuery),
                array('filter' => $searchQuery->getFilter())
        );
        
        return $output;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function handleNonSearchableRepository($repository, $searchQuery)
    {
        if (! $searchQuery instanceof SearchQuery) {
            throw new \InvalidArgumentException();
        }
        
        $filter = $searchQuery->getFilter();
        $term = implode(' ', $searchQuery->getTerms());
        
        switch ($filter) {
            case 'rack':
                $id = (integer) str_replace('R', '', $term);
                break;
            case 'vial':
                $id = (integer) $term;
                break;
            default:
                $id = false;
        }
        
        if ((false !== $id)&&($id > 0)) {
            $url = $this->generateUrl($this->getSearchRealm() . "_" . $filter . '_show', array('id' => $id));
            
            return $this->redirect($url);
        } else {
            throw $this->createNotFoundException();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function createSearchForm()
    {
        return new SearchType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function createAdvancedSearchForm()
    {
        return new AdvancedSearchType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchRealm()
    {
        return 'vib_flies';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function createSearchQuery($advanced = false)
    {
        $searchQuery = new SearchQuery($advanced);
        $searchQuery->setSecurityContext($this->getSecurityContext());
        
        return $searchQuery;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadSearchQuery()
    {
        $searchQuery = parent::loadSearchQuery();
        
        if (! $searchQuery instanceof SearchQuery) {
            throw $this->createNotFoundException();
        }
        
        $searchQuery->setSecurityContext($this->getSecurityContext());
        
        return $searchQuery;
    }
}
