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

namespace VIB\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base class for controllers
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AbstractController extends Controller
{

    /**
     * Get security context
     *
     * @return \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->get('security.context');
    }

    /**
     * Get object manager
     *
     * @return \VIB\BaseBundle\Doctrine\ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->get('vib.doctrine.manager');
    }

    /**
     * Get session
     *
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * Get paginator
     *
     * @return \Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * Get current page
     *
     * @return integer
     */
    protected function getCurrentPage()
    {
        return $this->getRequest()->query->get('page', 1);
    }

    /**
     * Get ACL filter
     *
     * @return \VIB\SecurityBundle\Bridge\Doctrine\AclHelper
     */
    protected function getAclFilter()
    {
        return $this->get('vib.security.helper.acl');
    }

    /**
     * Adds a flash message for type
     *
     * @param string $type
     * @param string $message
     */
    protected function addSessionFlash($type, $message)
    {
        $this->getSession()->getFlashBag()->add($type, $message);
    }
}
