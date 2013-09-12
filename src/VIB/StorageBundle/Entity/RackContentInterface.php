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


/**
 * Rack content interface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface RackContentInterface extends ContentInterface
{
    /**
     * Get position
     *
     * @return VIB\StorageBundle\Entity\RackPositionInterface
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param VIB\StorageBundle\Entity\RackPositionInterface $position
     */
    public function setPosition(RackPositionInterface $position = null);
    
    /**
     * Get previous position
     *
     * @return VIB\StorageBundle\Entity\RackPositionInterface
     */
    public function getPreviousPosition();

    /**
     * Set previous position
     *
     * @param VIB\StorageBundle\Entity\RackPositionInterface $prevPosition
     */
    public function setPreviousPosition(RackPositionInterface $prevPosition = null);
    
    /**
     * Get rack
     *
     * @return VIB\StorageBundle\Entity\RackInterface
     */
    public function getRack();

    /**
     * Set rack
     *
     * @param VIB\StorageBundle\Entity\RackInterface $rack
     */
    public function setRack(RackInterface $rack = null);
}
