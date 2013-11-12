<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\FliesBundle\Search;

use VIB\SearchBundle\Search\SearchQuery as BaseSearchQuery;

/**
 * SearchQuery class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchQuery extends BaseSearchQuery
{
    /**
     * Search terms
     * 
     * @var string
     */
    protected $filter;

    
    /**
     * Construct SearchQuery
     * 
     */
    public function __construct($advanced = false) {
        parent::__construct($advanced);
        $this->filter = null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getEntityClass() {
        $filter = $this->getFilter();
        $lookup = $this->getClassLookupTable();
        return key_exists($filter, $lookup) ? $lookup[$filter] : null;
    }

    /**
     * Get filter
     * 
     * @return string
     */
    public function getFilter() {
        
        if (null == $this->filter) {
            $term = implode(' ', $this->getTerms());

            if (preg_match("/^R\d+$/",$term) === 1) {
                
                return 'rack';
            } elseif (is_numeric($term)) {
                
                return 'vial';
            } else {
                
                return 'stock';
            }
        }
        
        return $this->filter;
    }

    /**
     * Set filter
     * 
     * @param string $filter
     */
    public function setFilter($filter) {
        $this->filter = $filter;
    }
    
    /**
     * Get class lookup table
     * 
     * @return array
     */
    private function getClassLookupTable() {
        return array(
            'vial' => 'VIB\FliesBundle\Entity\Vial',
            'rack' => 'VIB\FliesBundle\Entity\Rack',
            'stock' => 'VIB\FliesBundle\Entity\Stock',
            'crossvial' => 'VIB\FliesBundle\Entity\CrossVial',
            'injectionvial' => 'VIB\FliesBundle\Entity\InjectionVial',
        );
    }
}
