<?php

namespace MpiCbg\FliesBundle\Wrapper\Barcode;

use MpiCbg\FliesBundle\Wrapper\Entity\ManagedEntity;

class FlyCross extends ManagedEntity {
    
    private $maleBarcode;
    private $virginBarcode;
    
    public function getMaleBarcode()
    {
        if ($this->entity->getMale() != null) {
            return $this->entity->getMale()->getId();
        }
    }
    
    public function setMaleBarcode($id)
    {
        $this->maleBarcode = $id;
        
        if ($id !== null) {
            $male = $this->em->find('MpiCbgFliesBundle:CultureBottle', $id);
            if ($male != null) {
                $this->entity->setMale($male);
            }
        }
    }
    
    public function getVirginBarcode()
    {
        if ($this->entity->getVirgin() != null) {
            return $this->entity->getVirgin()->getId();
        }
    }
    
    public function setVirginBarcode($id)
    {
        $this->virginBarcode = $id;
        
        if ($id !== null) {
            $virgin = $this->em->find('MpiCbgFliesBundle:CultureBottle', $id);
            if ($virgin != null) {
                $this->entity->setVirgin($virgin);
            }
        }
    }
}