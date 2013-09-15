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

namespace VIB\AntibodyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

use VIB\StorageBundle\Entity\Rack;
use VIB\StorageBundle\Entity\StorageUnitInterface;
use VIB\StorageBundle\Entity\StorageUnitContentInterface;
use VIB\StorageBundle\Entity\TermocontrolledInterface;


/**
 * Box class
 *
 * @ORM\Entity(repositoryClass="VIB\AntibodyBundle\Repository\BoxRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Box extends Rack implements StorageUnitContentInterface, TermocontrolledInterface
{
    /**
     * @ORM\OneToMany(targetEntity="BoxPosition", mappedBy="box", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $positions;

    /**
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="boxes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * 
     * @var VIB\AntibodyBundle\Entity\Store
     */
    protected $store;

    /**
     * {@inheritdoc}
     */
    public function getStorageUnit()
    {
        return $this->store;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStorageUnit(StorageUnitInterface $unit = null)
    {
        $this->store = $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemperature()
    {
        return (($unit = $this->getStorageUnit()) instanceof TermocontrolledInterface) ? 
            $unit->getTemperature() : 21.00;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLocation() {
        return (null !== ($unit = $this->getStorageUnit())) ? (string) $unit : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPositionClass() {
        return 'VIB\AntibodyBundle\Entity\BoxPosition';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPositionsProperty() {
        return 'positions';
    }

}
