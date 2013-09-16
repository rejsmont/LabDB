<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UniqueEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use VIB\CoreBundle\Entity\Entity;

/**
 * Antibody class
 *
 * @ORM\Entity(repositoryClass="VIB\AntibodyBundle\Repository\AntibodyRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Antibody extends Entity
{ 
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Antigen must be specified")
     *
     * @var string
     */
    protected $antigen;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $targetSpecies;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Host species must be specified")
     *
     * @var string
     */
    protected $hostSpecies;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Type must be specified")
     *
     * @var string
     */
    protected $type;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $clone;
    
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     *
     * @var float
     */
    protected $size;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * 
     * @var string
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
     * @ORM\OneToMany(targetEntity="Tube", mappedBy="antibody", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var Doctrine\Common\Collections\Collection
     */
    protected $tubes;

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="antibody", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $applications;
    
    
    /**
     * Construct Stock
     */
    public function __construct()
    {
        $this->verified = false;
        $this->tubes = new ArrayCollection();
    }

    /**
     * Get antigen
     * 
     * @return string
     */
    public function getAntigen() {
        return $this->antigen;
    }

    /**
     * Set antigen
     * 
     * @param string $antigen
     */
    public function setAntigen($antigen) {
        $this->antigen = $antigen;
    }

    /**
     * Get target species
     * 
     * @return string
     */
    public function getTargetSpecies() {
        return $this->targetSpecies;
    }

    /**
     * Set target species
     * 
     * @param string $targetSpecies
     */
    public function setTargetSpecies($targetSpecies) {
        $this->targetSpecies = $targetSpecies;
    }

    /**
     * Get host species
     * 
     * @return string
     */
    public function getHostSpecies() {
        return $this->hostSpecies;
    }

    /**
     * Set host species
     * 
     * @param string $hostSpecies
     */
    public function setHostSpecies($hostSpecies) {
        $this->hostSpecies = $hostSpecies;
    }

    /**
     * Get type
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     * 
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Get clone
     * 
     * @return string
     */
    public function getClone() {
        return $this->clone;
    }

    /**
     * Set clone
     * 
     * @param string $clone
     */
    public function setClone($clone) {
        $this->clone = $clone;
    }

    /**
     * Get size
     * 
     * @return float
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Set size
     * 
     * @param float $size
     */
    public function setSize($size) {
        $this->size = $size;
    }
    
    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
    
    /**
     * Set notes
     *
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set vendor
     *
     * @param string
     */
    public function setVendor($stockVendor)
    {
        $this->vendor = $stockVendor;
    }

    /**
     * Get info URL
     *
     * @return type
     */
    public function getInfoURL()
    {
        return $this->infoURL;
    }

    /**
     * Set info URL
     *
     * @return type
     */
    public function setInfoURL($infoURL)
    {
        $this->infoURL = $infoURL;
    }
    
    /**
     * Get applications
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }
    
    /**
     * Add application
     *
     * @param VIB\AntibodyBundle\Entity\Application $application
     */
    public function addApplication(Application $application = null)
    {
        $applications = $this->getApplications();
        if (null !== $application) {
            if (! $applications->contains($application)) {
                $applications->add($application);
            }
            if ($application->getAntibody() !== $this) {
                $application->setAntibody($this);
            }
        }
    }

    /**
     * Remove application
     *
     * @param VIB\AntibodyBundle\Entity\Application $application
     */
    public function removeApplication(Application $application = null)
    {
        $this->getApplications()->removeElement($application);
    }
    
    /**
     * Get tubes
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getTubes()
    {
        return $this->tubes;
    }
    
    /**
     * Add tube
     *
     * @param VIB\AntibodyBundle\Entity\Tube $tube
     */
    public function addTube(Tube $tube = null)
    {
        $tubes = $this->getTubes();
        if (null !== $tube) {
            if (! $tubes->contains($tube)) {
                $tubes->add($tube);
            }
            if ($tube->getAntibody() !== $this) {
                $tube->setAntibody($this);
            }
        }
    }

    /**
     * Remove tube
     *
     * @param VIB\AntibodyBundle\Entity\Tube $tube
     */
    public function removeTube(Tube $tube = null)
    {
        $this->getTubes()->removeElement($tube);
    }
}
