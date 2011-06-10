<?php

namespace MpiCbg\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author ejsmont
 * @ORM\Entity(repositoryClass=
 *             "MpiCbg\FliesBundle\Repository\FlyStockRepository")
 */
class FlyStock {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id; 

    /**
     * @ORM\Column(type="string", length="255")
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="CultureBottle", mappedBy="stock")
     */
    protected $bottles;
    
    /**
     * @ORM\ManyToOne(targetEntity="FlyCross")
     */
    protected $sourceCross;
    
    public function __construct()
    {
        $this->bottles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addBottles(new \MpiCbg\FliesBundle\Entity\CultureBottle());
        foreach ($this->getBottles() as $bottle) {
            $bottle->setStock($this);
        }
    }
    
    public function __toString() {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get label
     *
     * @return string $name
     */
    public function getLabel()
    {
        return $this->name;
    }
    
    /**
     * Add bottles
     *
     * @param MpiCbg\FliesBundle\Entity\CultureBottle $bottles
     */
    public function addBottles(\MpiCbg\FliesBundle\Entity\CultureBottle $bottles)
    {
        $this->bottles[] = $bottles;
    }

    /**
     * Get bottles
     *
     * @return Doctrine\Common\Collections\Collection $bottles
     */
    public function getBottles()
    {
        return $this->bottles;
    }

    /**
     * Set sourceCross
     *
     * @param MpiCbg\FliesBundle\Entity\FlyCross $sourceCross
     */
    public function setSourceCross(\MpiCbg\FliesBundle\Entity\FlyCross $sourceCross)
    {
        $this->sourceCross = $sourceCross;
    }

    /**
     * Get sourceCross
     *
     * @return MpiCbg\FliesBundle\Entity\FlyCross $sourceCross
     */
    public function getSourceCross()
    {
        return $this->sourceCross;
    }
}