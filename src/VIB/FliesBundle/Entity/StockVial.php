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
 * StockVial class
 *
 * @ORM\Entity(repositoryClass="VIB\FliesBundle\Repository\StockVialRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockVial extends Vial implements AltLabelInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="StockVial", inversedBy="children")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="StockVial", mappedBy="parent", fetch="EXTRA_LAZY")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="Stock", inversedBy="vials")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank(message = "Stock must be specified")
     * @Serializer\Expose
     */
    protected $stock;

    /**
     * {@inheritdoc}
     */
    protected function inheritFromTemplate(Vial $template = null)
    {
        parent::inheritFromTemplate($template);
        if ($template instanceof StockVial) {
            $this->setStock($template->getStock());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelText()
    {
        if (null !== $this->getStock()) {
            return $this->getStock()->getLabel();
        } else {
            return parent::getLabelText();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLabelText()
    {
        if (null !== $this->getStock()) {
            return $this->getStock()->getGenotype();
        } else {
            return parent::getLabelText();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(Vial $child)
    {
        parent::addChild($child);
        if ($child instanceof StockVial) {
            $child->setStock($this->getStock());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(Vial $parent = null)
    {
        parent::setParent($parent);
        if ($parent instanceof StockVial) {
            $this->setStock($parent->getStock());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @Assert\True(message = "Parent vial must hold a stock")
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
        return 'stock';
    }

    /**
     * Set stock
     *
     * @param \VIB\FliesBundle\Entity\Stock $stock
     */
    public function setStock(Stock $stock = null)
    {
        $this->stock = $stock;
    }

    /**
     * Get stock
     *
     * @return \VIB\FliesBundle\Entity\Stock
     */
    public function getStock()
    {
        return $this->stock;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGenotypes()
    {   
        return array((null !== $this->getStock()) ? $this->getStock()->getGenotype() : null);
    }
}
