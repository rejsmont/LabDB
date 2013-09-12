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
 * Rack interface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface RackInterface
{
    /**
     * Return string representation of Rack
     *
     * @return string
     */
    public function __toString();
    
    /**
     * Get position
     *
     * @param  string                                         $row
     * @param  integer                                        $column
     * @return VIB\StorageBundle\Entity\RackPositionInterface
     * @throws OutOfBoundsException
     */
    public function getPosition($row, $column);

    /**
     * Count rows in rack
     *
     * @return integer
     */
    public function getRows();

    /**
     * Count columns in rack
     *
     * @return integer
     */
    public function getColumns();

    /**
     * Get geometry
     *
     * @return string
     */
    public function getGeometry();

    /**
     * Set geometry
     *
     * @param integer $rows
     * @param integer $columns
     */
    public function setGeometry($rows, $columns);
    
    /**
     * Get contents at $row $column
     *
     * @param  string                                        $row
     * @param  integer                                       $column
     * @return VIB\StorageBundle\Entity\RackContentInterface
     */
    public function getContent($row, $column);

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContents();

    /**
     * Add content (to first empty position)
     *
     * @param  VIB\StorageBundle\Entity\RackContentInterface $content
     * @param  string                                        $row
     * @param  integer                                       $column
     * @return boolean
     */
    public function addContent(RackContentInterface $content, $row = null, $column = null);

    /**
     * Remove content
     *
     * @param VIB\StorageBundle\Entity\RackContentInterface $content
     */
    public function removeContent(RackContentInterface $content);

    /**
     * Replace content at given position
     *
     * @param string                                        $row
     * @param integer                                       $column
     * @param VIB\StorageBundle\Entity\RackContentInterface $vial
     */
    public function replaceContent($row, $column, RackContentInterface $content = null);

    /**
     * Clear contents
     *
     */
    public function clearContents();

    /**
     * Check if content is in the rack
     *
     * @param  VIB\StorageBundle\Entity\RackContentInterface $content
     * @return boolean
     */
    public function hasContent(RackContentInterface $content);
}
