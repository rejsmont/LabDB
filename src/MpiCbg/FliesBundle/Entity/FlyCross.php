<?php

namespace MpiCbg\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author ejsmont
 * @ORM\Entity(repositoryClass=
 *             "MpiCbg\FliesBundle\Repository\FlyCrossRepository")
 */
class FlyCross {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id; 
    
    /**
     * @ORM\ManyToOne(targetEntity="CultureBottle", inversedBy="maleCrosses")
     */
    protected $male;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     */
    protected $maleName;
    
    /**
     * @ORM\ManyToOne(targetEntity="CultureBottle", inversedBy="virginCrosses")
     */
    protected $virgin;
        
    /**
     * @ORM\Column(type="string", length="255", nullable="true")
     */
    protected $virginName;
    
    /**
     * @ORM\OneToOne(targetEntity="CultureBottle", mappedBy="cross")
     */
    protected $bottle;
    
    /**
     * @ORM\OneToMany(targetEntity="FlyStock", mappedBy="sourceCross")
     */
    protected $stocks;

    public function __construct()
    {
        $this->bottle = new \MpiCbg\FliesBundle\Entity\CultureBottle;
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
        return $this->virginName . " ‚Äö√≤√∏ ‚Äö√∫√Ø " . $this->maleName . " ‚Äö√¥√á";
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
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $male
     */
    public function setMale(\MpiCbg\FliesBundle\Entity\CultureBottle $male)
    {
        $this->male = $male;
    }

    /**
     * Get male
     *
     * @return MpiCbg\FliesBundle\Entity\CultureBottle $male
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
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $virgin
     */
    public function setVirgin(\MpiCbg\FliesBundle\Entity\CultureBottle $virgin)
    {
        $this->virgin = $virgin;
    }

    /**
     * Get virgin
     *
     * @return MpiCbg\FliesBundle\Entity\CultureBottle $virgin
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
     * Set bottle
     *
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $bottle
     */
    public function setBottle(\MpiCbg\FliesBundle\Entity\CultureBottle $bottle)
    {
        $bottle->setCross($this);
        $this->bottle = $bottle;
    }

    /**
     * Get bottle
     *
     * @return MpiCbg\FliesBundle\Entity\CultureBottle $bottle
     */
    public function getBottle()
    {
        return $this->bottle;
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
        return $this->getBottle()->getCrosses();
    }
}