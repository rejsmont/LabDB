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

use VIB\CoreBundle\Entity\NamedEntityInterface;
use VIB\StorageBundle\Entity\RackContent;
use VIB\StorageBundle\Entity\RackInterface;
use VIB\StorageBundle\Entity\RackPositionInterface;
use VIB\StorageBundle\Entity\StorageUnitInterface;
use VIB\StorageBundle\Entity\StorageUnitContentInterface;
use VIB\StorageBundle\Entity\TermocontrolledInterface;
use VIB\FliesBundle\Label\LabelDateInterface;

/**
 * Vial class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\VialRepository")
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="alive_idx", columns={"id", "setupDate", "trashed", "type"}),
 *         @ORM\Index(name="stock_idx", columns={"id", "setupDate", "trashed", "type", "stock_id"}),
 *         @ORM\Index(name="cross_idx", columns={"id", "setupDate", "trashed", "type", "maleName", "virginName"}),
 *         @ORM\Index(name="injection_idx", columns={
 *             "id", "setupDate", "trashed", "type", "targetStock_id", "constructName"
 *         })
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "vial" = "Vial",
 *     "stock" = "StockVial",
 *     "cross" = "CrossVial",
 *     "injection" = "InjectionVial"})
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Vial extends RackContent
    implements NamedEntityInterface, StorageUnitContentInterface, TermocontrolledInterface, LabelDateInterface
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
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Expose
     */
    protected $defaultFlipDate;

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
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     */
    protected $food;

    /**
     * @var null
     */
    protected $children;

    /**
     * @var null 
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
     * @ORM\ManyToOne(targetEntity="RackPosition")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $prevPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Incubator", inversedBy="vials")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $incubator;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $temperature;
    
    /**
     * Construct a new vial
     *
     * If $template is set, inherit properties from the template.
     * If $flip is true, become a child of the template.
     *
     * @param VIB\FliesBundle\Entity\Vial $template
     * @param boolean                     $flip
     */
    public function __construct(Vial $template = null, $flip = true)
    {
        $this->temperature = null;
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
            $this->updateStorageConditions();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf("%06d",$this->getId());
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string) $this->getId();
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
            $this->setFood($template->getFood());
            $this->setNotes($template->getNotes());
            $this->setStorageUnit($template->getStorageUnit());
        }
    }

    /**
     * Set setupDate
     *
     * @param DateTime $setupDate
     */
    public function setSetupDate($setupDate)
    {
        $this->setupDate = $setupDate;
        $this->updateStorageConditions();
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
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return (null !== $this->size) ? $this->size : 'medium';
    }

    /**
     * Set size
     *
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Get food
     *
     * @return string
     */
    public function getFood()
    {
        return ucfirst((null !== $this->food) ? $this->food : 'Normal');
    }

    /**
     * Set food
     *
     * @param string $size
     */
    public function setFood($food)
    {
        $this->food = ucfirst($food);
    }
    
    /**
     * Add child
     *
     * @param VIB\FliesBundle\Entity\Vial $child
     */
    public function addChild(Vial $child)
    {
        $this->getChildren()->add($child);
    }

    /**
     * Remove child
     *
     * @param VIB\FliesBundle\Entity\Vial $child
     */
    public function removeChild(Vial $child)
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
     * Set parent
     *
     * @param VIB\FliesBundle\Entity\Vial $parent
     */
    public function setSourceVial(Vial $parent = null)
    {
        $this->setParent($parent);
    }

    /**
     * Get parent
     *
     * @return VIB\FliesBundle\Entity\Vial
     */
    public function getSourceVial()
    {
        return $this->getParent();
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
            ->where(Criteria::expr()->eq('trashed', false))
            ->andWhere(Criteria::expr()->gt('setupDate', $date));

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
     * {@inheritdoc}
     */
    public function getStorageUnit() {
        if (($rack = $this->getRack()) instanceof StorageUnitContentInterface) {
            return $rack->getStorageUnit();
        } else {
            return $this->incubator;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStorageUnit(StorageUnitInterface $unit = null) {
        $this->incubator = $unit;
        $this->updateStorageConditions();
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(RackPositionInterface $position = null)
    {
        parent::setPosition($position);
        $this->updateStorageConditions();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setRack(RackInterface $rack = null)
    {
        parent::setRack($rack);
        $this->updateStorageConditions();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->isAlive() ? parent::getLocation() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemperature()
    {
        if (null === $this->temperature) {
            $this->updateTemperature();
        }
        return $this->temperature;
    }
    
    /**
     * 
     */
    protected function getStorageUnitTemperature()
    {
        $unit = $this->getStorageUnit();
        if (($unit instanceof TermocontrolledInterface)&&(! $this->isTrashed())) {
            
            return $unit->getTemperature();
        }
        
        return null;
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
        if (null === $this->defaultFlipDate) {
            $this->updateDefaultFlipDate();
        }
        
        return $this->defaultFlipDate;
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
     * Is vial dead
     *
     * @return boolean
     */
    public function isDead()
    {
        return ! $this->isAlive();
    }

    /**
     * Get genotypes of flies in this vial
     * 
     * @return array
     */
    public function getGenotypes()
    {
        return array();
    }
    
    /**
     * Return unique genotype of flies in the vial
     * 
     * @return string|false
     */
    public function getGenotype()
    {
        $genotypes = $this->getGenotypes();
        
        return (count($genotypes) == 1) ? $genotypes[0] : false;
    }
    
    /**
     * Was this vial used?
     * 
     * @return boolean
     */
    public function wasUsed()
    {
        if ($this->getChildren()->count() > 0) {
            
            return true;
        } else if ($this->getVirginCrosses()->count() > 0) {
            
            return true;
        } else if ($this->getMaleCrosses()->count() > 0) {
            
            return true;
        } else {
            
            return false;
        }
    }
    
    /**
     * Should this vial be flipped soon?
     * 
     * @return boolean
     */
    public function isDue()
    {
        $weekAgo = new \DateTime();
        $weekAgo->sub(new \DateInterval('P1W'));
        $inOneWeek = new \DateTime();
        $inOneWeek->add(new \DateInterval('P1W'));
        
        return (($this->getRealFlipDate() > $weekAgo)&&
                ($this->getRealFlipDate() < $inOneWeek));
    }
    
    /**
     * Should have this vial been flipped long time ago?
     * 
     * @return boolean
     */
    public function isOverDue()
    {
        $weekAgo = new \DateTime();
        $weekAgo->sub(new \DateInterval('P1W'));
        
        return (($this->getRealFlipDate() <= $weekAgo)&&
                (! $this->wasUsed()));
    }
    
    /**
     * Update temperature
     */
    protected function updateTemperature()
    {
        $storageUnitTemperature = $this->getStorageUnitTemperature();
        if (null !== $storageUnitTemperature) {
            $this->temperature = $storageUnitTemperature;
        } elseif ((null === $this->temperature)||($this->isAlive())) {
            $this->temperature = 21.00;
        }
    }
    
    /**
     * Update default flip date
     */
    protected function updateDefaultFlipDate()
    {
        $interval = new \DateInterval('P' . 2 * $this->getGenerationTime() . 'D');
        if (null !== $this->getSetupDate()) {
            $flipDate = clone $this->getSetupDate();
            $flipDate->add($interval);
        } else {
            $flipDate = null;
        }
        
        $this->defaultFlipDate = $flipDate;        
    }
    
    /**
     * Update temperature and default flip date
     */
    public function updateStorageConditions()
    {
        $this->updateTemperature();
        $this->updateDefaultFlipDate();
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
    
    /**
     * {@inheritdoc}
     */
    protected function getPositionProperty() {
        return 'position';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreviousPositionProperty() {
        return 'prevPosition';
    }

}
