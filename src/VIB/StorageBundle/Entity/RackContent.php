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

namespace VIB\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use VIB\CoreBundle\Entity\Entity;


/**
 * Content class
 *
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class RackContent extends Entity implements RackContentInterface
{
    /**
     * Get the name of position property
     * 
     * @return string
     */
    abstract protected function getPositionProperty();
    
    /**
     * Get the name of previous position property
     * 
     * @return string
     */
    abstract protected function getPreviousPositionProperty();
    
    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->{$this->getPositionProperty()};
    }
    
    /**
     * {@inheritdoc}
     */
    public function setPosition(RackPositionInterface $position = null)
    {
        $prevPosition = $this->getPosition();
        $this->setPreviousPosition($prevPosition);
        $this->{$this->getPositionProperty()} = $position;
        if ((null !== $prevPosition)&&(null === $position)) {
            $prevPosition->setContent(null);
        }
        if ((null !== $position)&&($position->getContent() !== $this)) {
            $position->setContent($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPosition()
    {
        return $this->{$this->getPreviousPositionProperty()};
    }

    /**
     * {@inheritdoc}
     */
    public function setPreviousPosition(RackPositionInterface $previousPosition = null)
    {
        $this->{$this->getPreviousPositionProperty()} = $previousPosition;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRack()
    {
        $position = $this->getPosition();
        
        return (null !== $position) ? $position->getRack() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setRack(RackInterface $rack = null)
    {
        $rack->addContent($this);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        $position = $this->getPosition();
        $rack = $this->getRack();
        $location = (string) $position;
        
        if ($rack instanceof ContentInterface) {
            $rackLocation = $rack->getLocation();
            $location = ($rackLocation == '') ? $location : $rackLocation . ' ' . $location;
        }
        
        return $location;
    }
}
