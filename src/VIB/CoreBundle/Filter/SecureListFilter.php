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

namespace VIB\CoreBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Description of SecureListFilter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SecureListFilter implements ListFilterInterface, SecureFilterInterface {
    
    protected $access;
    
    protected $securityContext;
    
    /**
     * Construct SecureListFilter
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function __construct(Request $request = null, SecurityContextInterface $securityContext = null)
    {
        $this->access = (null !== $request) ? $request->get('access', 'private') : 'private';
        $this->securityContext = $securityContext;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAccess()
    {
        return $this->access;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }
        
    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        if ($this->access == 'private') {
            return array('OWNER');
        } elseif ($this->access == 'shared') {
            return array('OPERATOR');
        } else {
            if (null !== $this->securityContext) {
                return $this->securityContext->isGranted('ROLE_ADMIN') ? false : array('VIEW');
            } else {
                return array('VIEW');
            }
        } 
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        if ((null === $this->securityContext)||(null === $token = $this->securityContext->getToken())) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
}
