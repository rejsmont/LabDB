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

namespace VIB\StorageBundle\Entity;


/**
 * Rack position interface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface RackPositionInterface
{
    /**
     * Return string representation of RackPosition
     *
     * @return string
     */
    public function __toString();

    /**
     * Get row (as string)
     *
     * @return string
     */
    public function getRow();

    /**
     * Alias for getRackColumn
     *
     * @return type
     */
    public function getColumn();

    /**
     * Does this position have provided coordinates
     *
     * @param  string  $row
     * @param  integer $column
     * @return boolean
     */
    public function isAt($row, $column);

    /**
     * Get rack
     *
     * @return VIB\StorageBundle\Entity\RackInterface
     */
    public function getRack();

    /**
     * Get content
     *
     * @return VIB\StorageBundle\Entity\ContentInterface
     */
    public function getContent();
    
    /**
     * Set content
     *
     * @param VIB\StorageBundle\Entity\ContentInterface $contents
     */
    public function setContent(RackContentInterface $contents = null);

    /**
     * It this position empty
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Set previous contents
     *
     * @param VIB\StorageBundle\Entity\ContentInterface $prevContent
     */
    public function setPreviousContent(RackContentInterface $previousContent = null);
}
