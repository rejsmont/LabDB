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

namespace VIB\AntibodyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use VIB\StorageBundle\Entity\RackContent;
use VIB\StorageBundle\Entity\TermocontrolledInterface;

/**
 * Tube class
 *
 * @ORM\Entity(repositoryClass="VIB\AntibodyBundle\Repository\TubeRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Tube extends RackContent implements TermocontrolledInterface
{
    
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     */
    protected $type;

    /**
     * @ORM\OneToOne(targetEntity="BoxPosition", inversedBy="contents")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="BoxPosition", inversedBy="prevContents")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $prevPosition;

    /**
     * Construct new tube
     * 
     */
    public function __construct()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf("%06d",$this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getTemperature()
    {
        $box = $this->getPosition()->getRack();
        
        return ($box instanceof TermocontrolledInterface) ? $box->getTemperature() : 21;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getPositionProperty() {
        return 'position';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreviousPositionProperty() {
        return 'prevPosition';
    }

}
