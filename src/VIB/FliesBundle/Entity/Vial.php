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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use VIB\CoreBundle\Entity\Entity;
use VIB\FliesBundle\Label\LabelDateInterface;

/**
 * Vial class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\VialRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"vial" = "Vial", "stock" = "StockVial", "cross" = "CrossVial"})
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Vial extends Entity implements LabelDateInterface
{
    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message = "Setup date must be specified")
     * @Serializer\Expose
     */
    protected $setupDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Expose
     */
    protected $flipDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     */
    protected $notes;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     */
    protected $size;

    /**
     * @ORM\OneToMany(targetEntity="Vial", mappedBy="parent", fetch="EXTRA_LAZY")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Vial", inversedBy="children")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="CrossVial", mappedBy="virgin", fetch="EXTRA_LAZY")
     */
    protected $virginCrosses;

    /**
     * @ORM\OneToMany(targetEntity="CrossVial", mappedBy="male", fetch="EXTRA_LAZY")
     */
    protected $maleCrosses;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    protected $labelPrinted;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    protected $trashed;

    /**
     * @ORM\OneToOne(targetEntity="RackPosition", inversedBy="contents")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="RackPosition", inversedBy="prevContents")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $prevPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Incubator", inversedBy="vials")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $incubator;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $temperature;

    /**
     * Construct a new vial
     *
     * If $template is set, inherit properties from the template.
     * If $flip is true, become a child of the template.
     *
     * @param VIB\FliesBundle\Entity\Vial $template
     * @param boolean                      $flip
     */
    public function __construct(Vial $template = null, $flip = true)
    {
        $this->temperature = 21.00;
        $this->children = new ArrayCollection();
        $this->virginCrosses = new ArrayCollection();
        $this->maleCrosses = new ArrayCollection();
        $this->setLabelPrinted(false);
        $this->setTrashed(false);
        if (null !== $template) {
            $this->inheritFromTemplate($template);
            if ($flip) {
                $this->setParent($template);
                $this->resetDates($template);
            }
        } else {
            $this->resetDates();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelBarcode()
    {
        return sprintf("%06d",$this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelText()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelDate()
    {
        return $this->getSetupDate();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Reset dates
     *
     * @param VIB\FliesBundle\Entity\Vial $template
     */
    private function resetDates(Vial $template = null)
    {
        $this->setSetupDate(new \DateTime());
        if ((null !== $template)&&
            (null !== $template->getFlipDate())&&
            (null !== $template->getSetupDate())) {
            $flipDate = new \DateTime();
            $flipDate->add($template->getSetupDate()->diff($template->getFlipDate()));
            $this->setFlipDate($flipDate);
        } else {
            $this->setFlipDate(null);
        }
    }

    /**
     * Inherit properties from template
     *
     * @param VIB\FliesBundle\Entity\Vial $template
     */
    protected function inheritFromTemplate(Vial $template = null)
    {
        if (null !== $template) {
            $this->setSetupDate($template->getSetupDate());
            $this->setFlipDate($template->getFlipDate());
            $this->setSize($template->getSize());
            $this->setNotes($template->getNotes());
            $this->setIncubator($template->getIncubator());
        }
    }

    /**
     * Return string representation of Vial
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%06d",$this->getId());
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->getId();
    }

    /**
     * Set setupDate
     *
     * @param DateTime $setupDate
     */
    public function setSetupDate($setupDate)
    {
        $this->setupDate = $setupDate;
    }

    /**
     * Get setupDate
     *
     * @return DateTime
     */
    public function getSetupDate()
    {
        return $this->setupDate;
    }

    /**
     * Set flipDate
     *
     * @param DateTime $flipDate
     */
    public function setFlipDate($flipDate)
    {
        $this->flipDate = $flipDate;
    }

    /**
     * Get flipDate
     *
     * @return DateTime
     */
    public function getFlipDate()
    {
        return $this->flipDate;
    }

    /**
     * Set notes
     *
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Get notes
     *
     * @return type
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get size
     *
     * @return type
     */
    public function getSize()
    {
        return (null !== $this->size) ? $this->size : 'medium';
    }

    /**
     * Set size
     *
     * @return type
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Add child
     *
     * @param VIB\FliesBundle\Entity\Vial $child
     */
    public function addChild(Vial $child = null)
    {
        $this->getChildren()->add($child);
    }

    /**
     * Remove child
     *
     * @param VIB\FliesBundle\Entity\Vial $child
     */
    public function removeChild(Vial $child = null)
    {
        $this->getChildren()->removeElement($child);
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param VIB\FliesBundle\Entity\Vial $parent
     */
    public function setParent(Vial $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return VIB\FliesBundle\Entity\Vial
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Check if parent vial is of matching type
     *
     * @return boolean
     */
    public function isParentValid()
    {
        return true;
    }

    /**
     * Get maleCrosses
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getMaleCrosses()
    {
        return $this->maleCrosses;
    }

    /**
     * Get virginCrosses
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getVirginCrosses()
    {
        return $this->virginCrosses;
    }

    /**
     * Get crosses
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getCrosses()
    {
        $crosses = new ArrayCollection();
        foreach ($this->getMaleCrosses() as $cross) {
            $crosses->add($cross);
        }
        foreach ($this->getVirginCrosses() as $cross) {
            $crosses->add($cross);
        }

        return $crosses;
    }

    /**
     * Get living crosses
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getLivingCrosses()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("trashed", "false"))
            ->andWhere(Criteria::expr()->gt("setupDate", $date));
        
        $livingCrosses = new ArrayCollection();
        foreach ($this->getMaleCrosses()->matching($criteria) as $cross) {
            $livingCrosses->add($cross);
        }
        foreach ($this->getVirginCrosses()->matching($criteria) as $cross) {
            $livingCrosses->add($cross);
        }

        return $livingCrosses;
    }

    /**
     * Is label printed
     *
     * @return boolean
     */
    public function isLabelPrinted()
    {
        return $this->labelPrinted;
    }

    /**
     * Set labelPrinted
     *
     * @param boolean $labelPrinted
     */
    public function setLabelPrinted($labelPrinted)
    {
        $this->labelPrinted = $labelPrinted;
    }

    /**
     * Is vial trashed
     *
     * @return boolean
     */
    public function isTrashed()
    {
        return $this->trashed;
    }

    /**
     * Set trashed
     *
     * @param boolean $trashed
     */
    public function setTrashed($trashed)
    {
        $this->trashed = $trashed;
        if ($trashed) {
            $this->temperature = $this->getTemperature();
            $this->setPosition(null);
        }
    }

    /**
     * Get position
     *
     * @return VIB\FliesBundle\Entity\RackPosition
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param VIB\FliesBundle\Entity\RackPosition $position
     */
    public function setPosition(RackPosition $position = null)
    {
        $prevPosition = $this->getPosition();
        $this->setPrevPosition($prevPosition);
        $this->position = $position;
        if ((null !== $prevPosition)&&(null === $position)) {
            $prevPosition->setContents(null);
        }
        if ((null !== $position)&&($position->getContents() !== $this)) {
            $position->setContents($this);
        }
    }

    /**
     * Get previous position
     *
     * @return VIB\FliesBundle\Entity\RackPosition
     */
    public function getPrevPosition()
    {
        return $this->prevPosition;
    }

    /**
     * Set previous position
     *
     * @param VIB\FliesBundle\Entity\RackPosition $prevPosition
     */
    public function setPrevPosition(RackPosition $prevPosition = null)
    {
        $this->prevPosition = $prevPosition;
    }

    /**
     * Get incubator
     *
     * @return VIB\FliesBundle\Entity\Incubator
     */
    public function getIncubator()
    {
        if ((($position = $this->getPosition()) instanceof RackPosition)&&
            (($rack = $position->getRack()) instanceof Rack)) {
            return $rack->getIncubator();
        } else {
            return $this->incubator;
        }
    }

    /**
     * Set incubator
     *
     * @param VIB\FliesBundle\Entity\Incubator $incubator
     */
    public function setIncubator(Incubator $incubator = null)
    {
        $this->incubator = $incubator;
    }

    /**
     * Get position
     *
     * @return VIB\FliesBundle\Entity\RackPosition
     */
    public function getLocation()
    {
        $incubator = (string) $this->getIncubator();
        $position = (string) $this->getPosition();
        $glue = (null !== $incubator)&&(null !== $position) ? " " : "";

        return $this->isAlive() ? $incubator . $glue . $position : null;
    }

    /**
     * Get temperature
     *
     * @return float The temperature vial is kept in
     */
    public function getTemperature()
    {
        $incubator = $this->getIncubator();
        if (($incubator instanceof Incubator)&&(! $this->isTrashed())) {
            return $incubator->getTemperature();
        } else {
            return ($this->temperature !== null) ? $this->temperature : 21.00;
        }
    }

    /**
     * Get generation time
     *
     * @return integer
     */
    public function getGenerationTime()
    {
        return round(7346.7 * pow($this->getTemperature(),-2.079));
    }

    /**
     * Get progress
     *
     * @return float
     */
    public function getProgress()
    {
        $today = new \DateTime();
        $interval = $this->getSetupDate()->diff($today);

        return $interval->format('%a') / $this->getGenerationTime();
    }

    /**
     * Get default flip date
     *
     * @return DateTime
     */
    public function getDefaultFlipDate()
    {
        $interval = new \DateInterval('P' . 2 * $this->getGenerationTime() . 'D');
        $flipDate = clone $this->getSetupDate();
        $flipDate->add($interval);

        return $flipDate;
    }

    /**
     * Get set or calculated value for flip date
     *
     * @return DateTime
     */
    public function getRealFlipDate()
    {
        return (null !== $this->getFlipDate()) ? $this->getFlipDate() : $this->getDefaultFlipDate();
    }

    /**
     * Is vial alive
     *
     * @return boolean
     */
    public function isAlive()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));

        return (($this->getSetupDate() > $date ? true : false) && (! $this->isTrashed()));
    }

    /**
     * Get vial type
     *
     * @return string
     */
    public function getType()
    {
        return '';
    }
}
