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

use VIB\CoreBundle\Entity\Entity;


/**
 * Application class
 *
 * @ORM\Entity(repositoryClass="VIB\AntibodyBundle\Repository\ApplicationRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Application extends Entity
{
    /**
     * @ORM\ManyToOne(targetEntity="Antibody", inversedBy="applications")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Antibody must be specified")
     *
     * @var VIB\AntibodyBundle\Entity\Antibody
     */
    protected $antibody;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Type must be specified")
     *
     * @var string
     */
    protected $type;
    
    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $dilution;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * 
     * @var string
     */
    protected $notes;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose
     *
     * @var boolean
     */
    protected $verified;
    
    /**
     * Construct application
     */ 
    public function __construct()
    {
        $this->type = 'Western blot';
        $this->verified = false;
    }
    
    /**
     * Get antibody
     * 
     * @return VIB\AntibodyBundle\Entity\Antibody
     */
    public function getAntibody() {
        return $this->antibody;
    }

    /**
     * Set antibody
     * 
     * @param \VIB\AntibodyBundle\Entity\Antibody $antibody
     */
    public function setAntibody(Antibody $antibody) {
        $this->antibody = $antibody;
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
     * Get dilution
     * 
     * @return string
     */
    public function getDilution() {
        return $this->dilution;
    }

    /**
     * Set dilution
     * 
     * @param string $dilution
     */
    public function setDilution($dilution) {
        $this->dilution = $dilution;
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
     * Set notes
     * 
     * @param string $notes
     */
    public function setNotes($notes) {
        $this->notes = $notes;
    }
    
    /**
     * Is application verified
     *
     * @return boolean
     */
    public function isVerified()
    {
        return (bool) $this->verified;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }
}
