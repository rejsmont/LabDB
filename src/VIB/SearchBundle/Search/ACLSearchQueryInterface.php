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
interface ACLSearchQueryInterface
{
    
    /**
     * Set the Security Context
     * 
     * @param Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function setSecurityContext($securityContext);
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     */
    public function getUser();
    
    /**
     * Get search permission mask
     *
     * @return mixed
     */
    public function getPermissions();
}
