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

use VIB\CoreBundle\Entity\Entity;
use VIB\UserBundle\Entity\SettingsInterface;
use VIB\UserBundle\Entity\User;

/**
 * RackPosition class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\RackPositionRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Settings extends Entity implements SettingsInterface
{
    /**
     * @ORM\OneToOne(targetEntity="VIB\UserBundle\Entity\User")
     * @Serializer\Expose
     *
     * @var \VIB\UserBundle\Entity\User
     */
    protected $user;
    
    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $crossLabelStyle;
    
    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $stockLabelStyle;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose
     *
     * @var boolean
     */
    protected $autoPrinting;
    
    /**
     * Get user
     * 
     * @return type
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set user
     * 
     * @param \VIB\UserBundle\Entity\User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }
    
    /**
     * Get cross label style
     * 
     * @return type
     */
    public function getCrossLabelStyle() {
        return $this->crossLabelStyle;
    }

    /**
     * Set cross label style
     * 
     * @param type $crossLabelStyle
     */
    public function setCrossLabelStyle($crossLabelStyle) {
        $this->crossLabelStyle = $crossLabelStyle;
    }
    
    /**
     * Get stock label style
     * 
     * @return type
     */
    public function getStockLabelStyle() {
        return $this->stockLabelStyle;
    }
    
    /**
     * Set stock label style
     * 
     * @param type $stockLabelStyle
     */
    public function setStockLabelStyle($stockLabelStyle) {
        $this->stockLabelStyle = $stockLabelStyle;
    }
    
    /**
     * Get auto printing
     * 
     * @return type
     */
    public function getAutoPrinting() {
        return $this->autoPrinting;
    }

    /**
     * Set auto printing
     * 
     * @param type $autoPrinting
     */
    public function setAutoPrinting($autoPrinting) {
        $this->autoPrinting = $autoPrinting;
    }
}
