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
    
    /**
     * Statistics for injection
     *
     * @Route("/stats/{id}")
     * @Template()
     *
     * @param mixed $id
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function statsAction($id)
    {
        $injection = $this->getEntity($id);
        $vials = $this->getObjectManager()->getRepository($this->entityClass)->findSimilar($injection);
        $sterile = new ArrayCollection();
        $success = new ArrayCollection();
        $fail = new ArrayCollection();
        $ongoing =  new ArrayCollection();
        $stocks = new ArrayCollection();
        $crosses = new ArrayCollection();
        $temps = new ArrayCollection();
        $embryos = 0;

        if (count($vials) == 0) {
            throw $this->createNotFoundException();
        }

        foreach ($vials as $vial) {
            $temp = (float) $vial->getTemperature();
            if (! $temps->contains($temp)) {
                $temps->add($temp);
            }
            foreach ($vial->getCrosses() as $cross) {
                if (! $crosses->contains($cross)) {
                    $crosses->add($cross);
                }
            }
            $embryos += $vial->getEmbryoCount();
        }

        foreach ($crosses as $cross) {
            switch ($cross->getOutcome()) {
                case 'successful':
                    $success->add($cross);
                    foreach ($cross->getStocks() as $childStock) {
                        if (! $stocks->contains($childStock)) {
                            $stocks->add($childStock);
                        }
                    }
                    break;
                case 'failed':
                    $fail->add($cross);
                    break;
                case 'sterile':
                    $sterile->add($cross);
                    break;
                default:
                    $ongoing->add($cross);
                    break;
            }
        }
        
        return array('injection' => $injection,
                     'embryos' => $embryos,
                     'vials' => $vials,
                     'crosses' => $crosses,
                     'sterile' => $sterile,
                     'fail' => $fail,
                     'success' => $success,
                     'ongoing' => $ongoing,
                     'stocks' => $stocks,
                     'temps' => $temps);
    }
}
