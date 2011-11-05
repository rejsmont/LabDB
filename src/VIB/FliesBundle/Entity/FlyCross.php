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

use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Annotation\Expose;

/**
 * FlyStock class
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\FlyCrossRepository")
 * @ExclusionPolicy("all")
 */
class FlyCross {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Expose
     */
    protected $id; 
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="maleCrosses")
     */
    protected $male;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     * @Expose
     */
    protected $maleName;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="virginCrosses")
     */
    protected $virgin;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     * @Expose
     */
    protected $virginName;
    
    /**
     * @ORM\OneToOne(targetEntity="FlyVial", mappedBy="cross")
     */
    protected $vial;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyStock", mappedBy="sourceCross")
     */
    protected $stocks;

    /**
     * Construct FlyCross
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */ 
    public function __construct()
    {
        $this->vial = new \VIB\FliesBundle\Entity\FlyVial;
        $this->vial->setCross($this);
    }
    
    /**
     * Return string representation of FlyCross
     *
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getVirginName() . " ☿ ✕ " . $this->getMaleName() . " ♂";
    }
    
    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getVirginName() . "\nX\n" . $this->getMaleName();
    }
    
    /**
     * Set male
     *
     * @param VIB\FliesBundle\Entity\FlyVial $male
     */
    public function setMale(\VIB\FliesBundle\Entity\FlyVial $male)
    {
        $this->male = $male;
        if ($this->male != null)
            if ($this->male->getStock() != null)
                $this->maleName = $this->male->getStock()->getName();
    }

    /**
     * Get male
     *
     * @return VIB\FliesBundle\Entity\FlyVial
     */
    public function getMale()
    {
        return $this->male;
    }
    
    /**
     * Set maleName
     *
     * @param string $maleName
     */
    public function setMaleName($maleName)
    {
        if ($this->male != null)
            if ($this->male->getStock() != null)
                $maleName = $this->male->getStock()->getName();
        $this->maleName = $maleName;
    }

    /**
     * Get maleName
     *
     * @return string
     */
    public function getMaleName()
    {
        return $this->maleName;
    }

    /**
     * Set virgin
     *
     * @param VIB\FliesBundle\Entity\FlyVial $virgin
     */
    public function setVirgin(\VIB\FliesBundle\Entity\FlyVial $virgin)
    {
        $this->virgin = $virgin;
        if ($this->virgin != null)
            if ($this->virgin->getStock() != null)
                $this->virginName = $this->virgin->getStock()->getName();
    }

    /**
     * Get virgin
     *
     * @return VIB\FliesBundle\Entity\FlyVial
     */
    public function getVirgin()
    {
        return $this->virgin;
    }
    
    /**
     * Set virginName
     *
     * @param string $virginName
     */
    public function setVirginName($virginName)
    {
        if ($this->virgin != null)
            if ($this->virgin->getStock() != null)
                $virginName = $this->virgin->getStock()->getName();
        $this->virginName = $virginName;
    }

    /**
     * Get maleName
     *
     * @return string
     */
    public function getVirginName()
    {
                return $this->virginName;
    }

    /**
     * Set vial
     *
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     */
    public function setVial(\VIB\FliesBundle\Entity\FlyVial $vial)
    {
        $bottle->setCross($this);
        $this->vial = $vial;
    }

    /**
     * Get vial
     *
     * @return VIB\FliesBundle\Entity\FlyVial
     */
    public function getVial()
    {
        return $this->vial;
    }

    /**
     * Get stocks
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getStocks()
    {
        return $this->stocks;
    }
    
    /**
     * Get crosses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCrosses()
    {
        return $this->getVial()->getCrosses();
    }
}