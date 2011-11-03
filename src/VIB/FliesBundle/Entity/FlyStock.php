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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Entity\FlyCross;

/**
 * FlyStock class
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\FlyStockRepository")
 * @ExclusionPolicy("all")
 */
class FlyStock {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Expose
     */
    protected $id; 

    /**
     * @ORM\Column(type="string", length="255")
     * @Expose
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyVial", mappedBy="stock")
     */
    protected $vials;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyCross")
     */
    protected $sourceCross;
    
    /**
     * Construct FlyStock
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */    
    public function __construct() {
        $this->vials = new ArrayCollection();
        
        $this->addVials(new FlyVial());
        
        foreach ($this->getVials() as $vial) {
            $vial->setStock($this);
        }
    }
    
    /**
     * Return string representation of FlyStock
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel() {
        return $this->name;
    }
    
    /**
     * Add vials
     *
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     */
    public function addVials(\VIB\FliesBundle\Entity\FlyVial $vial) {
        $this->vials[] = $vial;
    }

    /**
     * Get vials
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVials() {
        return $this->vials;
    }

    /**
     * Set sourceCross
     *
     * @param VIB\FliesBundle\Entity\FlyCross $sourceCross
     */
    public function setSourceCross(FlyCross $sourceCross) {
        $this->sourceCross = $sourceCross;
    }

    /**
     * Get sourceCross
     *
     * @return VIB\FliesBundle\Entity\FlyCross
     */
    public function getSourceCross() {
        return $this->sourceCross;
    }
}