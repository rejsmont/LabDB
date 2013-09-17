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

namespace VIB\AntibodyBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VIB\CoreBundle\Controller\CRUDController;

use VIB\AntibodyBundle\Form\AntibodyType;

/**
 * AntibodyController class
 *
 * @Route("/antibodies")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AntibodyController extends CRUDController
{
    /**
     * Construct AntibodyController
     *
     */
    public function __construct()
    {
        $this->entityClass = 'VIB\AntibodyBundle\Entity\Antibody';
        $this->entityName  = 'antibody|antibodies';
        
        $antibody = new \VIB\AntibodyBundle\Entity\Antibody;
        $application = new \VIB\AntibodyBundle\Entity\Application;
        
        $antibody->addApplication($application);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditForm()
    {
        return new AntibodyType();
    }
}
