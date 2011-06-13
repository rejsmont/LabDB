<?php

namespace MpiCbg\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use \DateTime;
use \DateInterval;

use \MpiCbg\FliesBundle\Entity\FlyStock;

/**
 * @author ejsmont
 * @ORM\Entity(repositoryClass=
 *             "MpiCbg\FliesBundle\Repository\FlyVialRepository")
 */
class FlyVial {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="barcode_sequence")
     */
    protected $id; 
    
    /**
     * @ORM\Column(type="datetime", name="setup_date")
     */
    protected $setupDate;
    
    /**
     * @ORM\Column(type="datetime", name="flip_date")
     */
    protected $flipDate;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyVial", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="children")
     */
    protected $parent;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyStock", inversedBy="vial")
     */
    protected $stock;
    
    /**
     * @ORM\OneToOne(targetEntity="FlyCross", inversedBy="vial")
     */
    protected $cross;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyCross", mappedBy="male")
     */
    protected $maleCrosses;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyCross", mappedBy="virgin")
     */
    protected $virginCrosses;
    
    /**
     * @ORM\Column(type="boolean", name="has_label", nullable="true")
     */
    protected $labelPrinted;
    
    /**
     * @ORM\Column(type="boolean", name="trashed", nullable="true")
     */
    protected $trashed;
    
    
    
    /**
     * Construct the FlyVial
     *
     * @param MpiCbg\FliesBundle\Entity\FlyVial $parent
     */
    public function __construct($parent = null)
    {
        $this->children = new ArrayCollection();
        $this->maleCrosses = new ArrayCollection();
        $this->virginCrosses = new ArrayCollection();
        $this->setupDate = new DateTime;
        $this->flipDate = new DateTime;
        $this->flipDate->add(new DateInterval('P14D'));
        $this->labelPrinted = false;
        $this->trashed = false;
        if ($parent != null) {
            $this->parent = $parent;
            $this->stock = $parent->getStock();
        }
    }
    
    /**
     * Return string representation of this object
     * 
     * @return string $string
     */
    public function __toString() {
        return $this->getName();
    }
    
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return sprintf("%06d",$this->id);
    }
    
    /**
     * Get name
     *
     * @return string $name
     */
    public function getLabelText()
    {
        if (isset($this->stock)) {
            return $this->stock->getLabel();
        } else if (isset($this->cross)) {
            return $this->cross->getLabel();
        } else {
            return null;
        }
            
    }
    
    /**
     * Set setupDate
     *
     * @param datetime $setupDate
     */
    public function setSetupDate($setupDate)
    {
        $this->setupDate = $setupDate;
    }

    /**
     * Get setupDate
     *
     * @return datetime $setupDate
     */
    public function getSetupDate()
    {
        return $this->setupDate;
    }

    /**
     * Set flipDate
     *
     * @param datetime $flipDate
     */
    public function setFlipDate($flipDate)
    {
        $this->flipDate = $flipDate;
    }

    /**
     * Get flipDate
     *
     * @return datetime $flipDate
     */
    public function getFlipDate()
    {
        return $this->flipDate;
    }

    /**
     * Add children
     *
     * @param MpiCbg\FliesBundle\Entity\FlyVial $children
     */
    public function addChildren(FlyVial $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param MpiCbg\FliesBundle\Entity\FlyVial $parent
     */
    public function setParent(FlyVial $parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * Get parent
     *
     * @return MpiCbg\FliesBundle\Entity\FlyVial $parent
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Set stock
     *
     * @param MpiCbg\FliesBundle\Entity\FlyStock $stock
     */
    public function setStock(FlyStock $stock)
    {
        $this->stock = $stock;
        if($stock != null) {
            $this->cross = null;
        }
    }

    /**
     * Get stock
     *
     * @return MpiCbg\FliesBundle\Entity\FlyStock $stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set cross
     *
     * @param MpiCbg\FliesBundle\Entity\FlyCross $cross
     */
    public function setCross($cross)
    {
        $this->cross = $cross;
        if($cross != null) {
            $this->stock = null;
        }
    }

    /**
     * Get cross
     *
     * @return MpiCbg\FliesBundle\Entity\FlyCross $cross
     */
    public function getCross()
    {
        return $this->cross;
    }
    
    /**
     * Get maleCrosses
     *
     * @return Doctrine\Common\Collections\Collection $maleCrosses
     */
    public function getMaleCrosses()
    {
        return $this->maleCrosses;
    }
    
    /**
     * Get virginCrosses
     *
     * @return Doctrine\Common\Collections\Collection $virginCrosses
     */
    public function getVirginCrosses()
    {
        return $this->virginCrosses;
    }
    
    /**
     * Get crosses
     *
     * @return Doctrine\Common\Collections\Collection $crosses
     */
    public function getCrosses()
    {
        $crosses = new ArrayCollection();
        
        foreach($this->maleCrosses as $cross) {
            $crosses->add($cross);
        }
        
        foreach($this->virginCrosses as $cross) {
            $crosses->add($cross);
        }
        
        return $crosses;
    }
    
    /**
     * Is labelPrinted
     * 
     * @return boolean $labelPrinted 
     */
    public function isLabelPrinted() {
        return $this->labelPrinted;
    }

    /**
     * Set labelPrinted
     *
     * @param boolean $labelPrinted 
     */
    public function setLabelPrinted($labelPrinted) {
        $this->labelPrinted = $labelPrinted;
    }
    
    /**
     * Is the vial trashed
     * 
     * @return boolean $trashed
     */
    public function isTrashed() {
        return $this->trashed;
    }

    /**
     * Set trashed
     * 
     * @param boolean $trashed 
     */
    public function setTrashed($trashed) {
        $this->trashed = $trashed;
    }
    
    /**
     * Is alive
     *
     * @return boolean $alive
     */
    public function isAlive()
    {
        $date = new \DateTime;
        $date->sub(new \DateInterval('P2M'));
        return $this->setupDate > $date ? true : false;
    }
}
