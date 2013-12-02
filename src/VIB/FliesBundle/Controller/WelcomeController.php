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

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VIB\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for FliesBundle
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class WelcomeController extends AbstractController
{
    /**
     * Print panel
     *
     * @Route("/")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $stockVials = $this->getVialStatistics('VIB\FliesBundle\Entity\StockVial');
        $crossVials = $this->getVialStatistics('VIB\FliesBundle\Entity\CrossVial');
        $injectionVials = $this->getVialStatistics('VIB\FliesBundle\Entity\InjectionVial');

        $racks = $this->getObjectManager()
                ->getRepository('VIB\FliesBundle\Entity\Rack')
                ->getRacksWithMyVials();
        
        $incubators = $this->getObjectManager()
                ->getRepository('VIB\FliesBundle\Entity\Incubator')
                ->getList();
        
        return array(
            'stockVials' => $stockVials,
            'crossVials' => $crossVials,
            'injectionVials' => $injectionVials,
            'racks' => $racks,
            'incubators' => $incubators
        );
    }
    
    protected function getVialStatistics($class)
    {
        $om = $this->getObjectManager();
        
        $options = array();
        $options['filter'] = null;
        $options['permissions'] = array('OWNER');
        
        $repository = $om->getRepository($class);
        $stats = array();
        $stats['count'] = $repository->getListCount($options);
        $options['filter'] = 'forgot';
        $stats['forgot'] = $repository->getListCount($options);
        $options['filter'] = 'due';
        $stats['due'] = $repository->getListCount($options);
        $options['filter'] = 'overdue';
        $stats['overdue'] = $repository->getListCount($options);
        return $stats;
    }
}
