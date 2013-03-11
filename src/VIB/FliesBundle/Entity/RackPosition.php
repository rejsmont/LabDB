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

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use VIB\BaseBundle\Entity\Entity;


/**
 * RackPosition class
 * 
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\RackPositionRepository")
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RackPosition extends Entity {

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Row must be specified")
     * 
     * @var string
     */
    protected $rackRow;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Column must be specified")
     * 
     * @var string
     */
    protected $rackColumn;
    
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
    
    /**
     * @ORM\OneToMany(targetEntity="Vial", mappedBy="prevPosition")
     * 
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $prevContents;
        
    
    /**
     * Construct RackPosition
     *
     * @param integer|string $row
     * @param integer $column
     */    
    public function __construct($row, $column) {
        $this->setRow($row);
        $this->setColumn($column);
    }
    
    /**
     * Return string representation of Rack
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getRack() . ' ' . $this->getRow() . sprintf("%02d",$this->getColumn());
    }
    
    /**
     * Get row (as integer)
     * 
     * @return integer
     */
    public function getRackRow() {
        return $this->rackRow;
    }

    /**
     * Get row (as string)
     * 
     * @return string
     */
    public function getRow() {
        $base = ord('z') - ord('a') + 1;
        $rest = $this->getRackRow();
        $characters = array();
        while($rest > $base) {
            $div = floor($rest / $base);
            $characters[] = chr(ord('a') + $div - 1);
            $rest = $rest % $base;
        }
        $characters[] = chr(ord('a') + $rest - 1);
        return strtoupper(implode(array_reverse($characters)));
    }
    
    /**
     * Set row
     * 
     * @param integer|string $rackRow
     */
    public function setRackRow($rackRow) {
        if (!is_numeric($rackRow) && is_string($rackRow)) {
            $base = ord('z') - ord('a') + 1;
            $characters = array_reverse(str_split(strtolower($rackRow)));
            $rackRow = 0;
            foreach ($characters as $exp => $character) {
                $rackRow += (ord($character) - ord('a') + 1) * pow($base,$exp);
            }
        }
        $this->rackRow = $rackRow;
    }
    
    /**
     * Alias for setRackRow
     * 
     * @param integer|string $rackRow
     */
    public function setRow($rackRow) {
        $this->setRackRow($rackRow);
    }

    /**
     * Get column
     * 
     * @return type
     */
    public function getRackColumn() {
        return $this->rackColumn;
    }
    
    /**
     * Alias for getRackColumn
     * 
     * @return type
     */
    public function getColumn() {
        return $this->getRackColumn();
    }

    /**
     * Set column
     * 
     * @param integer $rackColumn
     */
    public function setRackColumn($rackColumn) {
        $this->rackColumn = $rackColumn;
    }
    
    /**
     * Alias for setRackColumn
     * 
     * @param integer $rackColumn
     */
    public function setColumn($rackColumn) {
        $this->setRackColumn($rackColumn);
    }
    
    /**
     * Does this position have provided coordinates
     *
     * @param string $row
     * @param integer $column
     * @return boolean
     */
    public function isAt($row, $column) {
        return ((null === $row)||($this->getRow() == strtoupper($row))) &&
            ((null === $column)||($this->getColumn() == $column));
    }
    
    /**
     * Get rack
     * 
     * @return \VIB\FliesBundle\Entity\Rack
     */
    public function getRack() {
        return $this->rack;
    }

    /**
     * Set rack
     * 
     * @param \VIB\FliesBundle\Entity\Rack $rack
     */
    public function setRack($rack) {
        $this->rack = $rack;
    }

    /**
     * Get contents
     * 
     * @return \VIB\FliesBundle\Entity\Vial
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Set contents
     * 
     * @param \VIB\FliesBundle\Entity\Vial $contents
     */
    public function setContents(Vial $contents = null) {
        $prevContents = $this->getContents();
        $this->setPrevContents($prevContents);
        $prevContents->setPosition(null);
        $this->contents = $contents;
        if ((null !== $contents)&&($contents->getPosition() !== $this)) {
            $contents->setPosition($this);
        }
    }

    /**
     * It this position empty
     * 
     * @return boolean
     */
    public function isEmpty() {
        return (null === $this->getContents());
    }

    /**
     * Set previous contents
     * 
     * @param \VIB\FliesBundle\Entity\Vial $prevContents
     */
    public function setPrevContents(Vial $prevContents = null) {
        if ((null !== $prevContents)&&($prevContents->getPrevPosition() !== $this)) {
            $prevContents->setPrevPosition($this);
        }
    }
}
