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

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use VIB\FliesBundle\Label\AltLabelInterface;

/**
 * InjectionVial class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\StockVialRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class InjectionVial extends Vial implements AltLabelInterface
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $injectionType;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $constructName;
    
    /**
     * @ORM\ManyToOne(targetEntity="Stock")
     * @Serializer\Expose
     */
    protected $targetStock;
    
    /**
     * @ORM\ManyToOne(targetEntity="StockVial")
     * @Serializer\Expose
     */
    protected $targetStockVial;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Expose
     */
    protected $receiptDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $vendor;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $orderNo;
    
    /**
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "Embryo count must be greater than 0"
     * )
     */
    protected $embryoCount;
    
    
    /**
     * Construct InjectionVial
     *
     * @param \VIB\FliesBundle\Entity\InjectionVial $parent
     * @param boolean                               $flip
     */
    public function __construct(Vial $template = null, $flip = false)
    {
        $this->injectionType = 'phiC31';
        $this->embryoCount = 0;
        parent::__construct($template, $flip);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function inheritFromTemplate(Vial $template = null)
    {
        parent::inheritFromTemplate($template);
        if ($template instanceof InjectionVial) {
            $this->setInjectionType($template->getInjectionType());
            $this->setConstructName($template->getConstructName());
            $this->setTargetStock($template->getTargetStock());
            $this->setTargetStockVial($template->getTargetStockVial());
            $this->setEmbryoCount($template->getEmbryoCount());
            $this->setVendor($template->getVendor());
            $this->setOrderNo($template->getOrderNo());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLabelText()
    {
        $stock = $this->getTargetStock();
        if (null !== $stock) {
            return $this->getConstructName() . " ➔ " . $stock->getGenotype();
        }
        return $this->getLabelText();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getConstructName() . " ➔ " . $this->getTargetStock();
    }
    
    /**
     * {@inheritdoc}
     */
    public function addChild(Vial $child = null)
    {
        parent::addChild($child);
        if ($child instanceof InjectionVial) {
            $child->setInjectionType($this->getInjectionType());
            $child->setConstructName($this->getConstructName());
            $child->setTargetStock($this->getTargetStock());
            $child->setTargetStockVial($this->getTargetStockVial());
            $child->setEmbryoCount($this->getEmbryoCount());
            $child->setVendor($this->getVendor());
            $child->setOrderNo($this->getOrderNo());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(Vial $parent = null)
    {
        parent::setParent($parent);
        if ($parent instanceof InjectionVial) {
            $this->setInjectionType($parent->getInjectionType());
            $this->setConstructName($parent->getConstructName());
            $this->setTargetStock($parent->getTargetStock());
            $this->setTargetStockVial($parent->getTargetStockVial());
            $this->setEmbryoCount($parent->getEmbryoCount());
            $this->setVendor($parent->getVendor());
            $this->setOrderNo($parent->getOrderNo());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @Assert\True(message = "Parent vial must hold injected flies")
     */
    public function isParentValid()
    {
        return (null === $this->getParent())||($this->getType() == $this->getParent()->getType());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'injection';
    }

    /**
     * Get injection type
     *
     * @return string
     */
    public function getInjectionType() {
        return $this->injectionType;
    }

    /**
     * Set injection type
     *
     * @param string $injectionType
     */
    public function setInjectionType($injectionType) {
        $this->injectionType = $injectionType;
    }
     
    /**
     * Get injection type
     *
     * @return string
     */
    public function getConstructName() {
        return $this->constructName;
    }

    /**
     * Set injection type
     *
     * @param string $injectionType
     */
    public function setConstructName($constructName) {
        $this->constructName = $constructName;
    }
    
    /**
     * Set target stock
     *
     * @param \VIB\FliesBundle\Entity\Stock $stock
     */
    public function setTargetStock(Stock $targetStock = null)
    {
        $this->targetStock = $targetStock;
        $vial = $this->getTargetStockVial();
        if ((null !== $vial)&&($vial->getStock() !== $targetStock)) {
            $this->setTargetStockVial(null);
        }
    }

    /**
     * Get target stock
     *
     * @return \VIB\FliesBundle\Entity\Stock
     */
    public function getTargetStock()
    {
        $stockVial = $this->getTargetStockVial();
        if (null !== $stockVial) {
            $stock = $stockVial->getStock();
            if (null !== $stock) {
                
                return $stock;
            }
        }
        
        return $this->targetStock;
    }
    
    /**
     * Set target stock
     *
     * @param \VIB\FliesBundle\Entity\StockVial $targetStockVial
     */
    public function setTargetStockVial(StockVial $targetStockVial = null)
    {
        $this->targetStockVial = $targetStockVial;
        if (null !== $targetStockVial) {
            $this->setTargetStock($targetStockVial->getStock());
        }
    }

    /**
     * Get target stock
     *
     * @return \VIB\FliesBundle\Entity\StockVial
     */
    public function getTargetStockVial()
    {
        return $this->targetStockVial;
    }
    
    /**
     * Get receipt date
     *
     * @return DateTime
     */
    public function getReceiptDate() {
        return $this->receiptDate;
    }

    /**
     * Set receipt date
     *
     * @param DateTime $receiptDate
     */
    public function setReceiptDate($receiptDate) {
        $this->receiptDate = $receiptDate;
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set vendor
     *
     * @param string
     */
    public function setVendor($stockVendor)
    {
        $this->vendor = $stockVendor;
    }
    
    /**
     * Get order number
     *
     * @return string
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * Set order number
     *
     * @param string
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;
    }
    
    /**
     * Get embryo count
     *
     * @return integer
     */
    public function getEmbryoCount() {
        return $this->embryoCount;
    }

    /**
     * Set embryo count
     *
     * @return integer
     */
    public function setEmbryoCount($embryoCount) {
        $this->embryoCount = $embryoCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getProgress()
    {
        $parent = $this->getParent();
        if (null !== $parent) {
            
            return $parent->getProgress();
        }
        
        return parent::getProgress();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFlipDate()
    {
        $parent = $this->getParent();
        if (null !== $parent) {
            
            return $parent->getDefaultFlipDate();
        }
        $interval = new \DateInterval('P' . $this->getGenerationTime() . 'D');
        $setup = clone $this->getSetupDate();
        $setup->add($interval);

        return $setup;
    }

    /**
     * Delay development by 2 days for new crosses
     *
     * @return integer
     */
    protected function getDelay()
    {
        return (null === $this->getParent()) ? 2 : 0;
    }
}
