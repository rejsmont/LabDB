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

namespace VIB\SearchBundle\Search;

/**
 * SearchQuery class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class SearchQuery implements SearchQueryInterface
{
    /**
     * Search terms
     * 
     * @var array
     */
    protected $terms;
    
    /**
     * Excluded terms
     * 
     * @var array
     */
    protected $excluded;
    
    /**
     * Is search advanced
     * 
     * @var boolean
     */
    protected $advanced;
    
    /**
     * Construct SearchQuery
     * 
     */
    public function __construct($advanced = false) {
        $this->terms = array();
        $this->excluded = array();
        $this->advanced = $advanced;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTerms() {
        return $this->terms;
    }

    /**
     * {@inheritdoc}
     */
    public function setTerms($terms) {
        if (is_array($terms)) {
            $this->terms = $terms;
        } elseif (trim($terms) != '') {
            $this->terms = explode(' ', $terms);
        } else {
            $this->terms = array();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getExcluded() {
        return $this->excluded;
    }

    /**
     * {@inheritdoc}
     */
    public function setExcluded($excluded) {
        if (is_array($excluded)) {
            $this->excluded = $excluded;
        } elseif (trim($excluded) != '') {
            $this->excluded = explode(' ', $excluded);
        } else {
            $this->excluded = array();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function isAdvanced() {
        return $this->advanced;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvanced($advanced) {
        $this->advanced = $advanced;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return array();
    }
}
