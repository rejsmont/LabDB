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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Annotation\Expose;

use \DateTime;
use \DateInterval;

use VIB\FliesBundle\Entity\FlyStock;
use VIB\FliesBundle\Entity\FlyCross;


/**
 * FlyVial class
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\FlyVialRepository")
 * @ExclusionPolicy("all")
 */
class FlyVial {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="barcode_sequence")
     * @Expose
     */
    protected $id; 
    
    /**
     * @ORM\Column(type="datetime", name="setup_date")
     * @Expose
     */
    protected $setupDate;
    
    /**
     * @ORM\Column(type="datetime", name="flip_date")
     * @Expose
     */
    protected $flipDate;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyVial", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="children")
     */
    protected $parent;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyStock", inversedBy="vials", fetch="EAGER")
     * @Expose
     */
    protected $stock;
    
    /**
     * @ORM\OneToOne(targetEntity="FlyCross", inversedBy="vial", fetch="EAGER")
     * @Expose
     */
    protected $cross;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyCross", mappedBy="male")
     */
    protected $maleCrosses;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyCross", mappedBy="virgin")
     */
    protected $virginCrosses;
    
    /**
     * @ORM\Column(type="boolean", name="has_label", nullable="true")
     * @Expose
     */
    protected $labelPrinted;
    
    /**
     * @ORM\Column(type="boolean", name="trashed", nullable="true")
     * @Expose
     */
    protected $trashed;
    
    /**
     * Construct FlyVial
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */
    public function __construct($parent = null) {
        $this->children = new ArrayCollection();
        $this->maleCrosses = new ArrayCollection();
        $this->virginCrosses = new ArrayCollection();
        $this->setupDate = new DateTime;
        $this->flipDate = new DateTime;
        $this->flipDate->add(new DateInterval('P14D'));
        $this->labelPrinted = false;
        $this->trashed = false;
        $this->selected = false;
        if ($parent != null) {
            $this->parent = $parent;
            $this->stock = $parent->getStock();
        }
    }
    
    /**
     * Return string representation of FlyVial
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
    public function getId() {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return sprintf("%06d",$this->id);
    }
    
    /**
     * Get labelText
     *
     * @return string
     */
    public function getLabelText() {
        if (isset($this->stock)) {
            return $this->stock->getLabel();
        } else if (isset($this->cross)) {
            return $this->cross->getLabel();
        } else {
            return null;
        }
    }
    
    /**
     * Set setupDate
     *
     * @param datetime $setupDate
     */
    public function setSetupDate($setupDate) {
        $this->setupDate = $setupDate;
    }

    /**
     * Get setupDate
     *
     * @return datetime
     */
    public function getSetupDate() {
        return $this->setupDate;
    }

    /**
     * Set flipDate
     *
     * @param datetime $flipDate
     */
    public function setFlipDate($flipDate) {
        $this->flipDate = $flipDate;
    }

    /**
     * Get flipDate
     *
     * @return datetime
     */
    public function getFlipDate() {
        return $this->flipDate;
    }

    /**
     * Add child
     *
     * @param VIB\FliesBundle\Entity\FlyVial $child
     */
    public function addChildren(FlyVial $child) {
        $this->children[] = $children;
    }
    
    /**
     * Set children
     *
     * @param Doctrine\Common\Collections\Collection $children
     */
    public function setChildren(Collection $children) {
        $this->children = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */
    public function setParent(FlyVial $parent) {
        $this->parent = $parent;
    }
    
    /**
     * Get parent
     *
     * @return VIB\FliesBundle\Entity\FlyVial
     */
    public function getParent() {
        return $this->parent;
    }
    
    /**
     * Set stock
     *
     * @param VIB\FliesBundle\Entity\FlyStock $stock
     */
    public function setStock(FlyStock $stock)
    {
        $this->stock = $stock;
        if($stock != null) {
            $this->cross = null;
        }
    }

    /**
     * Get stock
     *
     * @return VIB\FliesBundle\Entity\FlyStock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set cross
     *
     * @param VIB\FliesBundle\Entity\FlyCross $cross
     */
    public function setCross(FlyCross $cross)
    {
        $this->cross = $cross;
        if($cross != null) {
            $this->stock = null;
        }
    }

    /**
     * Get cross
     *
     * @return VIB\FliesBundle\Entity\FlyCross
     */
    public function getCross()
    {
        return $this->cross;
    }
    
    /**
     * Get maleCrosses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMaleCrosses()
    {
        return $this->maleCrosses;
    }
    
    /**
     * Get virginCrosses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVirginCrosses()
    {
        return $this->virginCrosses;
    }
    
    /**
     * Get crosses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCrosses()
    {
        $crosses = new ArrayCollection();
        
        foreach($this->maleCrosses as $cross) {
            $crosses->add($cross);
        }
        
        foreach($this->virginCrosses as $cross) {
            $crosses->add($cross);
        }
        
        return $crosses;
    }
    
    /**
     * Is labelPrinted
     * 
     * @return boolean
     */
    public function isLabelPrinted() {
        return $this->labelPrinted;
    }

    /**
     * Set labelPrinted
     *
     * @param boolean $labelPrinted 
     */
    public function setLabelPrinted($labelPrinted) {
        $this->labelPrinted = $labelPrinted;
    }
    
    /**
     * Is vial trashed
     * 
     * @return boolean
     */
    public function isTrashed() {
        return $this->trashed;
    }

    /**
     * Set trashed
     * 
     * @param boolean $trashed 
     */
    public function setTrashed($trashed) {
        $this->trashed = $trashed;
    }
    
    /**
     * Is alive
     *
     * @return boolean
     */
    public function isAlive()
    {
        $date = new \DateTime;
        $date->sub(new \DateInterval('P2M'));
        return $this->setupDate > $date ? true : false;
    }
}
