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

namespace VIB\AntibodyBundle\Search;

use JMS\Serializer\Annotation as Serializer;

use VIB\SearchBundle\Search\ACLSearchQuery;

/**
 * SearchQuery class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchQuery extends ACLSearchQuery
{
    /**
     * Search terms
     * 
     * @Serializer\Type("string")
     * 
     * @var string
     */
    protected $filter;
    
    /**
     * {@inheritdoc}
     */
    public function __construct($advanced = false) {
        parent::__construct($advanced);
        $this->filter = null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getEntityClass() {
        return 'VIB\AntibodyBundle\Entity\Antibody';
    }

    /**
     * Get filter
     * 
     * @return string
     */
    public function getFilter() {
        
        if (empty($this->filter)) {
            
            return 'all';
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
}
