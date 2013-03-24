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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UniqueEntity;

use VIB\BaseBundle\Entity\Entity;


/**
 * Stock class
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\StockRepository")
 * @Serializer\ExclusionPolicy("all")
 * @UniqueEntity("name")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Stock extends Entity {

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Name must be specified")
     * 
     * @var string
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Genotype must be specified")
     * 
     * @var string
     */
    protected $genotype;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     */
    protected $notes;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * 
     * @var string
     */
    protected $vendor;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Assert\Url()
     * 
     * @var string
     */
    protected $infoURL;
    
    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     * @Serializer\Expose
     * 
     * @var string
     */
    protected $verified;
    
    /**
     * @ORM\OneToMany(targetEntity="StockVial", mappedBy="stock", cascade={"persist", "remove"})
     * 
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $vials;
    
    /**
     * @ORM\ManyToOne(targetEntity="CrossVial", inversedBy="stocks")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * 
     * @var \VIB\FliesBundle\Entity\CrossVial
     */
    protected $sourceCross;
    
    
    /**
     * Construct Stock
     *
     */    
    public function __construct() {
        $this->verified = false;
        $this->vials = new ArrayCollection();
        $this->addVial(new StockVial());
        foreach ($this->getVials() as $vial) {
            $vial->setStock($this);
        }
    }
    
    /**
     * Return string representation of Stock
     *
     * @return string
     */
    public function __toString() {
        return $this->getName();
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
        return $this->getName();
    }
    
    /**
     * Set genotype
     * 
     * @param string $genotype
     */
    public function setGenotype($genotype) {
        $this->genotype = preg_replace(array('/\s?,\s?/','/\s?\;\s?/','/\s?\\/\s?/'),array(', ','; ',' / '),$genotype);
    }
    
    /**
     * Get genotype
     * 
     * @return string
     */
    public function getGenotype() {
        return preg_replace(array('/\s?,\s?/','/\s?\;\s?/','/\s?\\/\s?/'),array(', ','; ',' / '),$this->genotype);
    }
    
    /**
     * Set notes
     * 
     * @param string $notes
     */
    public function setNotes($notes) {
        $this->notes = $notes;
    }
    
    /**
     * Get notes
     * 
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }
    
    /**
     * Get vendor
     * 
     * @return type
     */
    public function getVendor() {
        return $this->vendor;
    }

    /**
     * Set vendor
     * 
     * @return type
     */
    public function setVendor($stockVendor) {
        $this->vendor = $stockVendor;
    }

    /**
     * Get info URL
     * 
     * @return type
     */
    public function getInfoURL() {
        return $this->infoURL;
    }

    /**
     * Set info URL
     * 
     * @return type
     */
    public function setInfoURL($infoURL) {
        $this->infoURL = $infoURL;
    }

    /**
     * Is stock verified
     * 
     * @return type
     */
    public function isVerified() {
        return (bool)$this->verified;
    }

    /**
     * Set verified
     * 
     * @return type
     */
    public function setVerified($verified) {
        $this->verified = $verified;
    }

        
    /**
     * Add vial
     *
     * @param \VIB\FliesBundle\Entity\Vial $vial
     */
    public function addVial(Vial $vial = null) {
        $this->getVials()->add($vial);
    }
    
    /**
     * Remove vial
     *
     * @param \VIB\FliesBundle\Entity\Vial $vial
     */
    public function removeVial(Vial $vial = null) {
        $this->getVials()->removeElement($vial);
    }

    /**
     * Get vials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVials() {
        return $this->vials;
    }
    
    /**
     * Get living vials
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLivingVials() {
        
        $livingVials = new ArrayCollection();
        
        foreach ($this->vials as $vial) {
            if ($vial->isAlive())
                $livingVials->add($vial);
        }
        
        return $livingVials;
    }
    
    /**
     * Set sourceCross
     *
     * @param \VIB\FliesBundle\Entity\CrossVial $sourceCross
     */
    public function setSourceCross(CrossVial $sourceCross = null) {
        $this->sourceCross = $sourceCross;
    }

    /**
     * Get sourceCross
     *
     * @return \VIB\FliesBundle\Entity\CrossVial
     */
    public function getSourceCross() {
        return $this->sourceCross;
    }
}
