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
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation as Serializer;

use \DateTime;
use \DateInterval;
use \ReflectionClass;

use VIB\BaseBundle\Entity\Entity;


/**
 * Vial class
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\VialRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"vial" = "Vial", "stock" = "StockVial", "cross" = "CrossVial"})
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Vial extends Entity {
    
    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     */
    protected $setupDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     */
    protected $flipDate;
    
    /**
     * @ORM\OneToMany(targetEntity="Vial", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="Vial", inversedBy="children")
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="CrossVial", mappedBy="virgin")
     */
    protected $virginCrosses;
    
    /**
     * @ORM\OneToMany(targetEntity="CrossVial", mappedBy="male")
     */
    protected $maleCrosses;
    
    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    protected $labelPrinted;
    
    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    protected $trashed;
    
    
    /**
     * Construct Vial
     *
     * @param \VIB\FliesBundle\Entity\Vial $parent
     * @param boolean $flip
     */
    public function __construct(Vial $template = null, $flip = true) {
        $this->children = new ArrayCollection();
        $this->virginCrosses = new ArrayCollection();
        $this->maleCrosses = new ArrayCollection();
        $this->setLabelPrinted(false);
        $this->setTrashed(false);
        if (null !== $template) {
            if ($flip) {
                $this->setParent($template);
                $this->resetDates();
            } else {
                $this->inheritFromTemplate($template);
            }
        } else {
            $this->resetDates();
        }
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
     * Reset dates
     */
    private function resetDates() {
        $this->setSetupDate(new DateTime());
        $this->setFlipDate((new DateTime())->add(new DateInterval('P14D')));
    }
    
    /**
     * Inherit properties from template
     * 
     * @param \VIB\FliesBundle\Entity\Vial $template
     */
    protected function inheritFromTemplate(Vial $template) {
        $this->setSetupDate($template->getSetupDate());
        $this->setFlipDate($template->getFlipDate());
    }
    
    /**
     * Return string representation of Vial
     * 
     * @return string
     */
    public function __toString() {
        return sprintf("%06d",$this->getId());
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->getId();
    }
    
    /**
     * Get labelText
     *
     * @return string
     */
    public function getLabelText() {
        return $this->getName();
    }
    
    /**
     * Set setupDate
     *
     * @param \DateTime $setupDate
     */
    public function setSetupDate($setupDate) {
        $this->setupDate = $setupDate;
    }

    /**
     * Get setupDate
     *
     * @return \DateTime
     */
    public function getSetupDate() {
        return $this->setupDate;
    }

    /**
     * Set flipDate
     *
     * @param \DateTime $flipDate
     */
    public function setFlipDate($flipDate) {
        $this->flipDate = $flipDate;
    }

    /**
     * Get flipDate
     *
     * @return \DateTime
     */
    public function getFlipDate() {
        return $this->flipDate;
    }

    /**
     * Add child
     *
     * @param \VIB\FliesBundle\Entity\Vial $child
     */
    public function addChild(Vial $child) {
        $this->getChildren()->add($child);
    }
    
    /**
     * Remove child
     *
     * @param \VIB\FliesBundle\Entity\Vial $child
     */
    public function removeChild(Vial $child) {
        $this->getChildren()->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \VIB\FliesBundle\Entity\Vial $parent
     */
    public function setParent(Vial $parent) {
        $this->parent = $parent;
    }
    
    /**
     * Get parent
     *
     * @return \VIB\FliesBundle\Entity\Vial
     */
    public function getParent() {
        return $this->parent;
    }
    
    /**
     * Check if parent vial is of matching type
     * 
     * @return boolean
     */
    public function isParentValid() {
        return true;
    }
    
    /**
     * Get maleCrosses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMaleCrosses()
    {
        return $this->maleCrosses;
    }
    
    /**
     * Get virginCrosses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVirginCrosses()
    {
        return $this->virginCrosses;
    }
    
    /**
     * Get crosses
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * Get living crosses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLivingCrosses() {
        
        $livingCrosses = new ArrayCollection();
        
        foreach ($this->getCrosses() as $cross) {
            if ($cross->isAlive())
                $livingCrosses->add($cross);
        }
        
        return $livingCrosses;
    }
    
    /**
     * Is label printed
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
    public function isAlive() {
        $date = (new DateTime())->sub(new DateInterval('P2M'));
        return (($this->getSetupDate() > $date ? true : false) && (! $this->isTrashed()));
    }
    
    /**
     * Get vial type
     * 
     * @return string
     */
    public function getType() {
        return '';
    }
    
    /**
     * Flip vial
     * 
     * @return \VIB\FliesBundle\Entity\Vial 
     */
    public final function flip() {
        return new static($this,true);
    }
}
