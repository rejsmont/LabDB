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

use JMS\Serializer\Annotation as Serializer;

/**
 * ACLSearchQuery class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class ACLSearchQuery extends SearchQuery implements ACLSearchQueryInterface
{
    /**
     * Security context
     * 
     * @Serializer\Exclude
     * 
     * @var string
     */
    protected $securityContext;
    
    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPermissions() {
        return $this->securityContext->isGranted('ROLE_ADMIN') ? false : array('VIEW');
    }

    /**
     * {@inheritdoc}
     */
    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }
}
