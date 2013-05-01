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

namespace VIB\FliesBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Entity\Entity;
use \VIB\FliesBundle\Label\LabelInterface;

/**
 * Rack class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\RackRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Rack extends Entity implements LabelInterface
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="RackPosition", mappedBy="rack", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $positions;

    /**
     * @var integer
     */
    private $rows;

    /**
     * @var integer
     */
    private $columns;

    /**
     * @ORM\ManyToOne(targetEntity="Incubator", inversedBy="racks")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $incubator;

    /**
     * Construct Rack
     *
     * @param integer $rows
     * @param integer $columns
     */
    public function __construct($rows = null, $columns = null)
    {
        $this->positions = new ArrayCollection();
        $this->setGeometry($rows, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelBarcode()
    {
        return sprintf("R%06d",$this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelText()
    {
        return $this->getDescription();
    }

    /**
     * Return string representation of Rack
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("R%06d",$this->getId());
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getPositions()
    {
        return $this->positions;
    }

    /**
     * Get position
     *
     * @param  string                               $row
     * @param  integer                              $column
     * @return \VIB\FliesBundle\Entity\RackPosition
     * @throws \OutOfBoundsException
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
     * Get first empty position
     *
     * @param  string                               $row
     * @param  integer                              $column
     * @return \VIB\FliesBundle\Entity\RackPosition
     * @throws \OutOfBoundsException
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
     * @param string  $row
     * @param integer $column
     * @param mixed   $contents
     */
    protected function setPosition($row, $column, $contents = null)
    {
        $this->getPosition($row, $column)->setContents($contents);
    }

    /**
     * Update counters for rows and columns
     *
     */
    private function updateGeometry()
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

    /**
     * Count rows in rack
     *
     * @return integer
     */
    public function getRows()
    {
        $this->updateGeometry();

        return $this->rows;
    }

    /**
     * Count columns in rack
     *
     * @return integer
     */
    public function getColumns()
    {
        $this->updateGeometry();

        return $this->columns;
    }

    /**
     * Get geometry
     *
     * @return string
     */
    public function getGeometry()
    {
        $this->updateGeometry();

        return $this->rows . " ✕ " . $this->columns;
    }

    /**
     * Set geometry
     *
     * @param integer $rows
     * @param integer $columns
     */
    public function setGeometry($rows, $columns)
    {
        $this->updateGeometry();

        if (($this->rows == $rows)&&($this->columns == $columns)) {
            return;
        }

        if ((null !== $rows)&&(null !== $columns)) {
            $this->getPositions()->clear();
            for ($row = 1; $row <= $rows; $row++) {
                for ($column = 1; $column <= $columns; $column++) {
                    $this->positions[] = new RackPosition($this,$row,$column);
                }
            }
            $this->rows = $rows;
            $this->columns = $columns;
        }
    }

    /**
     * Get vial
     *
     * @param  string                       $row
     * @param  integer                      $column
     * @return \VIB\FliesBundle\Entity\Vial
     */
    public function getVial($row, $column)
    {
        return $this->getPosition($row, $column)->getContents();
    }

    /**
     * Get vials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVials()
    {
        $vials = new ArrayCollection();
        foreach ($this->getPositions() as $position) {
            $vials[] = $position->getContents();
        }

        return $vials;
    }

    /**
     * Add vial to first empty position
     *
     * @param \VIB\FliesBundle\Entity\Vial $vial
     * @param string                       $row
     * @param integer                      $column
     */
    public function addVial(Vial $vial, $row = null, $column = null)
    {
        $position = $this->getFirstEmptyPosition($row, $column);
        if ($position != null) {
            $position->setContents($vial);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove vial
     *
     * @param \VIB\FliesBundle\Entity\Vial $vial
     */
    public function removeVial(Vial $vial)
    {
        foreach ($this->getPositions() as $position ) {
            if ($position->getContents() == $vial) {
                $position->setContents(null);
            }
        }
    }

    /**
     * Replace vial at given position
     *
     * @param string                       $row
     * @param integer                      $column
     * @param \VIB\FliesBundle\Entity\Vial $vial
     */
    public function replaceVial($row, $column, Vial $vial = null)
    {
        $this->setPosition($row, $column, $vial);
    }

    /**
     * Clear all vial
     *
     */
    public function clearVials()
    {
        foreach ($this->getPositions() as $position ) {
            $position->setContents(null);
        }
    }

    /**
     * Check if a vial is in the rack
     *
     * @param  \VIB\FliesBundle\Entity\Vial $vial
     * @return boolean
     */
    public function hasVial(Vial $vial)
    {
        return $this->getVials()->contains($vial);
    }

    /**
     * Get incubator
     *
     * @return \VIB\FliesBundle\Entity\Incubator
     */
    public function getIncubator()
    {
        return $this->incubator;
    }

    /**
     * Set incubator
     *
     * @param \VIB\FliesBundle\Entity\Incubator $incubator
     */
    public function setIncubator($incubator)
    {
        $this->incubator = $incubator;
    }

    /**
     * Get temperature
     *
     * @return float The temperature rack is kept in
     */
    public function getTemperature()
    {
        return (($incubator = $this->getIncubator()) instanceof Incubator) ? $incubator->getTemperature() : 21.00;
    }
}
