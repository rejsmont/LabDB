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

use VIB\StorageBundle\Entity\RackPosition as BaseRackPosition;


/**
 * RackPosition class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\RackPositionRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RackPosition extends BaseRackPosition
{
    /**
     * @ORM\ManyToOne(targetEntity="Rack", inversedBy="positions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Rack must be specified")
     *
     * @var \VIB\FliesBundle\Entity\Rack
     */
    protected $rack;

    /**
     * @ORM\OneToOne(targetEntity="Vial", mappedBy="position")
     * @Serializer\Expose
     *
     * @var \VIB\FliesBundle\Entity\Vial
     */
    protected $contents;
    
    
    protected function getContentProperty() {
        return 'contents';
    }

    protected function getRackProperty() {
        return 'rack';
    }

}
