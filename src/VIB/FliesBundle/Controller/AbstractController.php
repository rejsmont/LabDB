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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\ORM\QueryBuilder;

/**
 * Base class for CRUD operations CRUDController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AbstractController extends Controller {
        
    private $entityManager;
    private $formFactory;
    private $request;
    private $aclProvider;
    private $securityContext;
    private $currentUser;
    
    /**
     * Construct CRUDController
     *
     */ 
    public function __construct()
    {
        $this->entityManager = null;
        $this->formFactory = null;
        $this->request = null;
        $this->aclProvider = null;
        $this->securityContext = null;
        $this->currentUser = null;
    }
    
    /**
     * Get current user
     * 
     * @return UserInterface 
     */
    protected function getCurrectUser() {
        if ($this->currentUser == null)
            $this->currentUser = $this->getSecurityContext()->getToken()->getUser();
        return $this->currentUser;
    }

    public function getEntityManager() {
        if ($this->entityManager == null)
            $this->entityManager = $this->get('doctrine.orm.entity_manager');
        return $this->entityManager;
    }

    public function getFormFactory() {
        if ($this->formFactory == null)
            $this->formFactory = $this->get('form.factory');
        return $this->formFactory;
    }

    public function getRequest() {
        if ($this->request == null)
            $this->request = $this->get('request');
        return $this->request;
    }
    
    public function getAclProvider() {
        if ($this->aclProvider == null)
            $this->aclProvider = $this->get('security.acl.provider');
        return $this->aclProvider;
    }

    public function getSecurityContext() {
        if ($this->securityContext == null)
            $this->securityContext = $this->get('security.context');
        return $this->securityContext;
    }


}
?>
