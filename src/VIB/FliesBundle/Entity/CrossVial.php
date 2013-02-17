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

use JMS\Serializer\Annotation as Serializer;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * CrossVial class
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\CrossVialRepository")
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVial extends Vial {
    
    /**
     * @ORM\ManyToOne(targetEntity="Vial", inversedBy="maleCrosses")
     * @Assert\NotNull(message = "Male must be specified")
     */
    protected $male;
        
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    protected $maleName;
    
    /**
     * @ORM\ManyToOne(targetEntity="Vial", inversedBy="virginCrosses")
     * @Assert\NotNull(message = "Virgin must be specified")
     */
    protected $virgin;
        
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    protected $virginName;
    
    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="sourceCross")
     */
    protected $stocks;

    

    /**
     * Construct CrossVial
     *
     * @param \VIB\FliesBundle\Entity\CrossVial $parent
     * @param boolean $flip
     */
    public function __construct(Vial $template = null, $flip = false) {
        parent::__construct($template, $flip);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function inheritFromTemplate(Vial $template) {
        parent::inheritFromTemplate($template);
        if ($template instanceof CrossVial) {
            $this->setMale($template->getMale());
            $this->setMaleName($template->getMaleName());
            $this->setVirgin($template->getVirgin());
            $this->setVirginName($template->getVirginName());
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLabelText()
    {
        return $this->getVirginName() . "\nX\n" . $this->getMaleName();
    }
    
    /**
     * {@inheritdoc}
     */
    public function addChild(Vial $child) {
        parent::addChild($child);
        if ($child instanceof CrossVial) {
            $this->setMale($child->getMale());
            $this->setMaleName($child->getMaleName());
            $this->setVirgin($child->getVirgin());
            $this->setVirginName($child->getVirginName());
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent(Vial $parent) {
        parent::setParent($parent);
        if ($parent instanceof CrossVial) {
            $this->setMale($parent->getMale());
            $this->setMaleName($parent->getMaleName());
            $this->setVirgin($parent->getVirgin());
            $this->setVirginName($parent->getVirginName());
        }
    }
    
    /**
     * {@inheritdoc}
     * 
     * @Assert\True(message = "Parent vial must hold a cross")
     */
    public function isParentValid() {
        return (null === $this->getParent())||($this->getType() == $this->getParent()->getType());
    }
    
    /**
     * {@inheritdoc}
     */
    public function getType() {
        return 'cross';
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->getVirginName() . " ☿ ✕ " . $this->getMaleName() . " ♂";
    }
    
    /**
     * Set male
     *
     * @param VIB\FliesBundle\Entity\Vial $male
     */
    public function setMale(\VIB\FliesBundle\Entity\Vial $male)
    {
        $this->male = $male;
        if ($this->male != null)
            if ($this->male instanceof StockVial)
                $this->maleName = $this->male->getStock()->getName();
    }

    /**
     * Get male
     *
     * @return VIB\FliesBundle\Entity\Vial
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
            if ($this->male instanceof StockVial)
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
     * Check if male name is specified when male source is a cross
     * 
     * @Assert\True(message = "Male name must be specified")
     * 
     * @return boolean
     */
    public function isMaleValid() {
        return ! (($this->getMale() instanceof CrossVial)&&(trim($this->getMaleName()) == ""));
    }
    
    /**
     * Set virgin
     *
     * @param VIB\FliesBundle\Entity\Vial $virgin
     */
    public function setVirgin(\VIB\FliesBundle\Entity\Vial $virgin)
    {
        $this->virgin = $virgin;
        if ($this->virgin != null)
            if ($this->virgin instanceof StockVial)
                $this->virginName = $this->virgin->getStock()->getName();
    }

    /**
     * Get virgin
     *
     * @return VIB\FliesBundle\Entity\Vial
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
            if ($this->virgin instanceof StockVial)
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
     * Check if virgin name is specified when virgin source is a cross
     * 
     * @Assert\True(message = "Virgin name must be specified")
     * 
     * @return boolean
     */
    public function isVirginValid() {
        return ! (($this->getVirgin() instanceof CrossVial)&&(trim($this->getVirginName()) == ""));
    }
    
    /**
     * Get stocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStocks()
    {
        return $this->stocks;
    }
}
