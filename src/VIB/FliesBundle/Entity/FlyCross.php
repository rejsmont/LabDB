<?php

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author ejsmont
 * @ORM\Entity(repositoryClass=
 *             "VIB\FliesBundle\Repository\FlyCrossRepository")
 */
class FlyCross {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id; 
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="maleCrosses")
     */
    protected $male;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     */
    protected $maleName;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyVial", inversedBy="virginCrosses")
     */
    protected $virgin;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     */
    protected $virginName;
    
    /**
     * @ORM\OneToOne(targetEntity="FlyVial", mappedBy="cross")
     */
    protected $vial;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyStock", mappedBy="sourceCross")
     */
    protected $stocks;

    public function __construct()
    {
        $this->bottle = new \VIB\FliesBundle\Entity\FlyVial;
        $this->bottle->setCross($this);
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
        return $this->virginName . " ☿ ✕ " . $this->maleName . " ♂";
    }
    
    /**
     * Get name
     *
     * @return string $name
     */
    public function getLabel()
    {
        return $this->virginName . "\nX\n" . $this->maleName;
    }
    
    /**
     * Set male
     *
     * @param VIB\FliesBundle\Entity\FlyVial $male
     */
    public function setMale(\VIB\FliesBundle\Entity\FlyVial $male)
    {
        $this->male = $male;
    }

    /**
     * Get male
     *
     * @return VIB\FliesBundle\Entity\FlyVial $male
     */
    public function getMale()
    {
        return $this->male;
    }
    
    /**
     * Set maleName
     *
     * @param string $maleName
     */
    public function setMaleName($maleName)
    {
        $this->maleName = $maleName;
    }

    /**
     * Get maleName
     *
     * @return string $maleName
     */
    public function getMaleName()
    {
        return $this->maleName;
    }

    /**
     * Set virgin
     *
     * @param VIB\FliesBundle\Entity\FlyVial $virgin
     */
    public function setVirgin(\VIB\FliesBundle\Entity\FlyVial $virgin)
    {
        $this->virgin = $virgin;
    }

    /**
     * Get virgin
     *
     * @return VIB\FliesBundle\Entity\FlyVial $virgin
     */
    public function getVirgin()
    {
        return $this->virgin;
    }
    
    /**
     * Set virginName
     *
     * @param string $virginName
     */
    public function setVirginName($virginName)
    {
        $this->virginName = $virginName;
    }

    /**
     * Get maleName
     *
     * @return string $virginName
     */
    public function getVirginName()
    {
        return $this->virginName;
    }

    /**
     * Set vial
     *
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     */
    public function setVial(\VIB\FliesBundle\Entity\FlyVial $vial)
    {
        $bottle->setCross($this);
        $this->vial = $vial;
    }

    /**
     * Get vial
     *
     * @return VIB\FliesBundle\Entity\FlyVial $vial
     */
    public function getVial()
    {
        return $this->vial;
    }

    /**
     * Get stocks
     *
     * @return Doctrine\Common\Collections\Collection $stocks
     */
    public function getStocks()
    {
        return $this->stocks;
    }
    
    /**
     * Get crosses
     *
     * @return Doctrine\Common\Collections\Collection $crosses
     */
    public function getCrosses()
    {
        return $this->getVial()->getCrosses();
    }
}