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

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Entity\Entity;

/**
 * Incubator class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\IncubatorRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Incubator extends Entity
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Name must be specified")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Rack", mappedBy="incubator", fetch="EXTRA_LAZY")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $racks;

    /**
     * @ORM\OneToMany(targetEntity="Vial", mappedBy="incubator", fetch="EXTRA_LAZY")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $vials;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Temperature must be specified")
     * @Assert\Range(
     *      min = 4,
     *      max = 42,
     *      minMessage = "Temperature cannot be lower than 4℃",
     *      maxMessage = "Temperature cannot be higher than 42℃"
     * )
     *
     * @var float
     */
    private $temperature;

    /**
     * Construct Incubator
     *
     * @param float $temperature
     */
    public function __construct($temperature = 25)
    {
        $this->name = 'New incubator';
        $this->temperature = $temperature;
        $this->racks = new ArrayCollection();
        $this->vials = new ArrayCollection();
    }

    /**
     * Return string representation of Incubator
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get racks
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRacks()
    {
        return $this->racks;
    }

    /**
     * Get vials
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVials()
    {
        return $this->vials;
    }

    /**
     * Get temperature
     *
     * @return float
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Set temperature
     *
     * @param float $temperature
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }
}
