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
interface SearchQueryInterface
{
    /**
     * Get search terms
     * 
     * @return array
     */
    public function getTerms();

    /**
     * Set search terms
     * 
     * @param mixed $terms
     */
    public function setTerms($terms);
    
    /**
     * Get excluded terms
     * 
     * @return array
     */
    public function getExcluded();

    /**
     * Set excluded terms
     * 
     * @param mixed $excluded
     */
    public function setExcluded($excluded);
    
    /**
     * Is search advanced
     * 
     * @return boolean
     */
    public function isAdvanced();

    /**
     * Set advanced
     * 
     * @param boolean $advanced
     */
    public function setAdvanced($advanced);
    
    /**
     * Get options
     * 
     * @return array
     */
    public function getOptions();
    
    /**
     * Get entity class
     * 
     * @return string
     */
    public function getEntityClass();
}
