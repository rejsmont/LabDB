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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use VIB\CoreBundle\Entity\Entity;

/**
 * RackPosition class
 *
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class RackPosition extends Entity implements RackPositionInterface
{
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
     * Construct RackPosition
     *
     * @param VIB\StorageBundle\Entity\RackInterface   $rack
     * @param integer|string                           $row
     * @param integer                                  $column
     */
    public function __construct(RackInterface $rack, $row, $column)
    {
        $this->setRack($rack);
        $this->setRow($row);
        $this->setColumn($column);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getRack() . ' ' . $this->getRow() . sprintf("%02d",$this->getColumn());
    }

    /**
     * Get the name of contents property
     * 
     * @return string
     */
    abstract protected function getContentProperty();
    
    /**
     * Get the name of rack property
     * 
     * @return string
     */
    abstract protected function getRackProperty();
    
    /**
     * @see getRow
     */
    protected function getRackRow()
    {
        return $this->rackRow;
    }

    /**
     * {@inheritdoc}
     */
    public function getRow()
    {
        return $this->numberToRow($this->getRackRow());
    }

    /**
     * @see setRow
     */
    protected function setRackRow($rackRow)
    {
        $this->rackRow = $this->rowToNumber($rackRow);
    }

    /**
     * Set row
     * 
     * @param string|integer $row
     */
    protected function setRow($row)
    {
        $this->setRackRow($row);
    }

    /**
     * @see getColumn
     */
    protected function getRackColumn()
    {
        return $this->rackColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return $this->getRackColumn();
    }

    /**
     * @see setColumn
     */
    protected function setRackColumn($rackColumn)
    {
        $this->rackColumn = $rackColumn;
    }

    /**
     * Set column
     * 
     * @param integer $column
     */
    protected function setColumn($column)
    {
        $this->setRackColumn($column);
    }

    /**
     * {@inheritdoc}
     */
    public function isAt($row, $column)
    {
        return ((null === $row)||($this->getRackRow() == $this->rowToNumber($row))) &&
               ((null === $column)||($this->getColumn() == $column));
    }

    /**
     * {@inheritdoc}
     */
    public function getRack()
    {
        return $this->{$this->getRackProperty()};
    }

    /**
     * Set rack
     * 
     * @param VIB\StorageBundle\Entity\RackInterface $rack
     */
    protected function setRack(RackInterface $rack)
    {
        $this->{$this->getRackProperty()} = $rack;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->{$this->getContentProperty()};
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(RackContentInterface $contents = null)
    {
        $prevContent = $this->getContent();
        $this->setPreviousContent($prevContent);
        if (null !== $prevContent) {
            $prevContent->setPosition(null);
        }
        $this->{$this->getContentProperty()} = $contents;
        if ((null !== $contents)&&($contents->getPosition() !== $this)) {
            $contents->setPosition($this);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return (null === $this->getContent());
    }

    /**
     * {@inheritdoc}
     */
    public function setPreviousContent(RackContentInterface $previousContent = null)
    {
        if ((null !== $previousContent)&&($previousContent->getPreviousPosition() !== $this)) {
            $previousContent->setPreviousPosition($this);
        }
    }

    /**
     * 
     * @param  string  $row
     * @return integer
     */
    private function rowToNumber($row)
    {
        if (!is_numeric($row) && is_string($row)) {
            $base = ord('z') - ord('a') + 1;
            $characters = array_reverse(str_split(strtolower($row)));
            $row = 0;
            foreach ($characters as $exp => $character) {
                $row += (ord($character) - ord('a') + 1) * pow($base,$exp);
            }
        }

        return $row;
    }

    /**
     * 
     * @param  inetger $row
     * @return string
     */
    private function numberToRow($row)
    {
        if (is_numeric($row)) {
            $base = ord('z') - ord('a') + 1;
            $rest = $row;
            $characters = array();
            while ($rest > $base) {
                $div = floor($rest / $base);
                $characters[] = chr(ord('a') + $div - 1);
                $rest = $rest % $base;
            }
            $characters[] = chr(ord('a') + $rest - 1);

            return strtoupper(implode(array_reverse($characters)));
        } else {
            return $row;
        }
    }
}
