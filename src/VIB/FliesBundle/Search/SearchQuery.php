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
     * Search terms
     * 
     * @Serializer\Type("array")
     * 
     * @var array
     */
    protected $opts;
    
    /**
     * {@inheritdoc}
     */
    public function __construct($advanced = false) {
        parent::__construct($advanced);
        $this->filter = null;
        $this->opts = array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions() {        
        $options = array();
        $options['private'] = in_array('private', $this->opts);
        $options['dead'] = in_array('dead', $this->opts);
        $options['notes'] = in_array('notes', $this->opts);
        
        return $options;
    }
    
    /**
     * Search private only
     * 
     * @return boolean
     */
    public function searchPrivate() {
        $options =  $this->getOptions();
        return $options['private'];
    }
    
    /**
     * Search dead
     * 
     * @return boolean
     */
    public function searchDead() {
        $options =  $this->getOptions();
        return $options['dead'];
    }
    
    /**
     * Search notes
     * 
     * @return boolean
     */
    public function searchNotes() {
        $options =  $this->getOptions();
        return $options['notes'];
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
        
        if (empty($this->filter)) {
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
     * Get opts
     * 
     * @param string $filter
     */
    public function getOpts() {
        return $this->opts;
    }

    /**
     * Set opts
     * 
     * @param string $filter
     */
    public function setOpts($opts) {
        $this->opts = $opts;
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
    
    /**
     * {@inheritdoc}
     */
    public function getPermissions() {
        
        return $this->searchPrivate() ? array('OWNER') : parent::getPermissions();
    }
}
