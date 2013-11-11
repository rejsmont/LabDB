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

namespace VIB\SearchBundle\Repository;

/**
 * SearchableVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface SearchableRepositoryInterface
{
    /**
     * Get search Query
     * 
     * @param type $terms
     * @param type $excluded
     * @param type $options
     * @return Doctrine\ORM\Query
     */
    public function getSearchQuery($terms, $excluded = array(), $options = array());
        
    /**
     * Get search result count
     * 
     * @param array $terms
     * @param array $excluded
     * @param array $options
     * @return integer
     */
    public function getSearchResultCount($terms, $excluded = array(), $options = array());
}
