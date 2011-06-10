<?php

namespace MpiCbg\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author ejsmont
 * @ORM\Entity(repositoryClass=
 *             "MpiCbg\FliesBundle\Repository\CultureBottleRepository")
 */
class CultureBottle {
    
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
     * @ORM\OneToMany(targetEntity="CultureBottle", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="CultureBottle", inversedBy="children")
     */
    protected $parent;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyStock", inversedBy="bottle")
     */
    protected $stock;
    
    /**
     * @ORM\OneToOne(targetEntity="FlyCross", inversedBy="bottle")
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
    protected $hasLabel;
    
    /**
     * @ORM\Column(type="boolean", name="trashed", nullable="true")
     */
    protected $trashed;
    
    /**
     * Construct
     *
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $parent
     */
    public function __construct($parent = null)
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->maleCrosses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->virginCrosses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setupDate = new \DateTime;
        $this->flipDate = new \DateTime;
        $this->flipDate->add(new \DateInterval('P14D'));
        $this->hasLabel = false;
        $this->trashed = false;
        if ($parent != null) {
            $this->parent = $parent;
            $this->stock = $parent->getStock();
        }
    }
    
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
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $children
     */
    public function addChildren(\MpiCbg\FliesBundle\Entity\CultureBottle $children)
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
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $parent
     */
    public function setParent(\MpiCbg\FliesBundle\Entity\CultureBottle $parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * Get parent
     *
     * @return MpiCbg\FliesBundle\Entity\CultureBottle $parent
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
    public function setStock(\MpiCbg\FliesBundle\Entity\FlyStock $stock)
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
    //public function setCross(\MpiCbg\FliesBundle\Entity\FlyCross $cross)
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
        $crosses = new \Doctrine\Common\Collections\ArrayCollection();
        
        foreach($this->maleCrosses as $cross) {
            $crosses->add($cross);
        }
        
        foreach($this->virginCrosses as $cross) {
            $crosses->add($cross);
        }
        
        return $crosses;
    }
    
    /**
     * Get crosses
     *
     * @return boolean $alive
     */
    public function isAlive()
    {
        $date = new \DateTime;
        $date->sub(new \DateInterval('P2M'));
        return $this->setupDate > $date ? true : false;
    }
    
    public function getHasLabel() {
        return $this->hasLabel;
    }

    public function setHasLabel($hasLabel) {
        $this->hasLabel = $hasLabel;
    }
    
    public function isTrashed() {
        return $this->trashed;
    }

    public function setTrashed($trashed) {
        $this->trashed = $trashed;
    }


}
