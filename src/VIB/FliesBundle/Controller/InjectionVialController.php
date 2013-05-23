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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Form\InjectionVialType;
use VIB\FliesBundle\Form\InjectionVialNewType;

/**
 * InjectionVialController class
 *
 * @Route("/injections")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class InjectionVialController extends VialController
{

    /**
     * Construct InjectionVialController
     */
    public function __construct()
    {
        $this->entityClass = 'VIB\FliesBundle\Entity\InjectionVial';
        $this->entityName = 'injection|injections';
    }

    /**
     * Get object manager
     *
     * @return \VIB\FliesBundle\Doctrine\VialManager
     */
    protected function getObjectManager()
    {
        return $this->get('vib.doctrine.vial_manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateForm()
    {
        return new InjectionVialNewType();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditForm()
    {
        return new InjectionVialType();
    }

    /**
     * {@inheritdoc}
     */
    public function expandAction($id = null)
    {
        throw $this->createNotFoundException();
    }
}
