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

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

use VIB\StorageBundle\Entity\Rack as BaseRack;
use VIB\StorageBundle\Entity\StorageUnitInterface;
use VIB\StorageBundle\Entity\StorageUnitContentInterface;
use VIB\StorageBundle\Entity\TermocontrolledInterface;
use VIB\FliesBundle\Label\LabelInterface;

/**
 * Rack class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\RackRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Rack extends BaseRack implements LabelInterface, StorageUnitContentInterface, TermocontrolledInterface
{
    /**
     * @ORM\OneToMany(targetEntity="RackPosition", mappedBy="rack", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $positions;

    /**
     * @ORM\ManyToOne(targetEntity="Incubator", inversedBy="racks")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $incubator;

    /**
     * {@inheritdoc}
     */
    public function getLabelBarcode()
    {
        return sprintf("R%06d",$this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelText()
    {
        return $this->getName();
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->setName($description);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getName();
    }

    /**
     * @see getContent
     */
    public function getVial($row, $column)
    {
        return $this->getContent($row, $column);
    }

    /**
     * @see getContents
     */
    public function getVials()
    {
        return $this->getContents();
    }

    /**
     * @see addContent
     */
    public function addVial(Vial $vial, $row = null, $column = null)
    {
        return $this->addContent($vial, $row, $column);
    }

    /**
     * @see removeContent
     */
    public function removeVial(Vial $vial)
    {
        return $this->removeContent($vial);
    }

    /**
     * @see replaceContent
     */
    public function replaceVial($row, $column, Vial $vial = null)
    {
        $this->replaceContent($row, $column, $vial);
    }

    /**
     * @see clearContents
     */
    public function clearVials()
    {
        $this->clearContents();
    }

    /**
     * $see hasContent
     */
    public function hasVial(Vial $vial)
    {
        return $this->hasContent($vial);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageUnit()
    {
        return $this->incubator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStorageUnit(StorageUnitInterface $unit = null)
    {
        $this->incubator = $unit;
    }
    
    /**
     * @see getStorageUnit
     */
    public function getIncubator()
    {
        return $this->incubator;
    }

    /**
     * @see setStorageUnit
     */
    public function setIncubator(Incubator $incubator = null)
    {
        $this->setStorageUnit($incubator);
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
        return 'VIB\FliesBundle\Entity\RackPosition';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPositionsProperty() {
        return 'positions';
    }

}
