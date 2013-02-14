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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Entity\FlyCross;

/**
 * FlyStock class
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\FlyStockRepository")
 * @Assert\Callback(methods={"isSourceCrossVialValid"})
 * @Serializer\ExclusionPolicy("all")
 */
class FlyStock {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Serializer\Expose
     * 
     * @var integer;
     */
    protected $id; 

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Name cannot be blank")
     * 
     * @var string
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyVial", mappedBy="stock", cascade={"persist", "remove"})
     * 
     * @var Doctrine\Common\Collections\Collection
     */
    protected $vials;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyCross", inversedBy="stocks")
     * 
     * @var VIB\FliesBundle\Entity\FlyCross
     */
    protected $sourceCross;
    
    /**
     * @var VIB\FliesBundle\Entity\FlyVial
     */
    protected $sourceCrossVial;
    
    
    /**
     * Construct FlyStock
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */    
    public function __construct() {
        
        $this->sourceCrossVial = null;
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
     * Get routable id
     *
     * @return integer
     */
    public function getRoutableId()
    {
        return $this->getId();
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
    
    /**
     * Set sourceCrossVial
     *
     * @param VIB\FliesBundle\Entity\FlyVial $sourceCrossVial
     */
    public function setSourceCrossVial(FlyVial $sourceCrossVial) {
        $this->sourceCrossVial = $sourceCrossVial;
        $this->sourceCross = null !== $sourceCrossVial ? $sourceCrossVial->getCross() : null;
    }

    /**
     * Get sourceCrossVial
     *
     * @return VIB\FliesBundle\Entity\FlyVial
     */
    public function getSourceCrossVial() {
        if (null === $this->sourceCrossVial) {
            return null !== $this->sourceCross ? $this->sourceCross->getVial() : null;
        } else {
            return $this->sourceCrossVial;
        }
    }
    
    /**
     * Check if source cross vial is valid
     * 
     * @Assert\True(message = "Vial does not hold a cross")
     * 
     * @return boolean
     */
    public function isSourceCrossVialValid() {
        $sourceCrossVial = null !== $this->sourceCross ? $this->sourceCross->getVial() : null;
        return $sourceCrossVial === $this->getSourceCrossVial();
    }
}
