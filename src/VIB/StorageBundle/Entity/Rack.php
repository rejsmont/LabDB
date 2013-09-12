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

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Entity\NamedEntity;

/**
 * Rack class
 *
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class Rack extends NamedEntity implements RackInterface
{
    /**
     * @var integer
     */
    private $rows;

    /**
     * @var integer
     */
    private $columns;

    
    /**
     * Construct Rack
     *
     * @param integer $rows
     * @param integer $columns
     */
    public function __construct($rows = null, $columns = null)
    {
        $this->{$this->getPositionsProperty()} = new ArrayCollection();
        $this->name = 'New rack';
        $this->setGeometry($rows, $columns);
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf("R%06d",$this->getId());
    }
    
    /**
     * Get the name of positions property
     * 
     * @return string
     */
    abstract protected function getPositionsProperty();
    
    /**
     * Get the name of position class
     * 
     * @return string
     */
    abstract protected function getPositionClass();

    /**
     * {@inheritdoc}
     */
    public function getPosition($row, $column)
    {
        foreach ($this->getPositions() as $position) {
            if ($position->isAt($row, $column)) {
                
                return $position;
            }
        }
        throw new \OutOfBoundsException();
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        $this->updateGeometry();

        return $this->rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $this->updateGeometry();

        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeometry()
    {
        $this->updateGeometry();

        return $this->rows . " ✕ " . $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function setGeometry($rows, $columns)
    {
        if (($rows > 0)&&($columns > 0)) {
            $this->updateGeometry();
            if (($this->rows != $rows)||($this->columns != $columns)) {
                $positionClass = $this->getPositionClass();
                $this->getPositions()->clear();
                for ($row = 1; $row <= $rows; $row++) {
                    for ($column = 1; $column <= $columns; $column++) {
                        $this->positions[] = new $positionClass($this, $row, $column);
                    }
                }
                $this->rows = $rows;
                $this->columns = $columns;
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContent($row, $column)
    {
        return $this->getPosition($row, $column)->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $contents = new ArrayCollection();
        foreach ($this->getPositions() as $position) {
            if (($content = $position->getContent()) !== null) {
                $contents[] = $content;
            }
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function addContent(RackContentInterface $content, $row = null, $column = null)
    {
        $position = $this->getFirstEmptyPosition($row, $column);
        if ($position != null) {
            $position->setContent($content);

            return true;
        } else {
            
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeContent(RackContentInterface $content)
    {
        foreach ($this->getPositions() as $position ) {
            if ($position->getContent() === $content) {
                $position->setContent(null);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceContent($row, $column, RackContentInterface $content = null)
    {
        $this->setPosition($row, $column, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function clearContents()
    {
        foreach ($this->getPositions() as $position ) {
            $position->setContent(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasContent(RackContentInterface $content)
    {
        return $this->getContents()->contains($content);
    }

    /**
     * Get positions
     *
     * @return Doctrine\Common\Collections\Collection
     */
    protected function getPositions()
    {
        return $this->{$this->getPositionsProperty()};
    }


    /**
     * Get first empty position
     *
     * @param  string                               $row
     * @param  integer                              $column
     * @return VIB\StorageBundle\Entity\RackPosition
     * @throws OutOfBoundsException
     */
    protected function getFirstEmptyPosition($row = null, $column = null)
    {
        foreach ($this->getPositions() as $position) {
            if ($position->isAt($row, $column) && $position->isEmpty()) {
                
                return $position;
            }
        }

        return null;
    }

    /**
     * Set position
     *
     * @param string                                     $row
     * @param integer                                    $column
     * @param VIB\StorageBundle\Entity\ContentInterface $contents
     */
    protected function setPosition($row, $column, RackContentInterface $content = null)
    {
        $this->getPosition($row, $column)->setContent($content);
    }

    /**
     * Update counters for rows and columns
     *
     */
    protected function updateGeometry()
    {
        if ((null === $this->rows)||(null === $this->columns)) {
            $rows = array();
            $columns = array();
            foreach ($this->getPositions() as $position) {
                $rows[$position->getRow()] = true;
                $columns[$position->getColumn()] = true;
            }
            $this->rows = count($rows);
            $this->columns = count($columns);
        }
    }
}
