<?php

namespace VIB\FliesBundle\Wrapper\Barcode;

use VIB\FliesBundle\Wrapper\Entity\ManagedEntity;

class FlyVial extends ManagedEntity {
    
    private $parentBarcode;
    
    public function getParentBarcode()
    {
        if ($this->entity->getParent() != null) {
            return $this->entity->getParent()->getId();
        }
    }
    
    public function setParentBarcode($id)
    {
        $this->parentBarcode = $id;
        
        if ($id !== null) {
            $parent = $this->em->find('VIBFliesBundle:FlyVial', $id);
            if ($parent != null) {
                $this->entity->setParent($parent);
            }
        }
    }
}