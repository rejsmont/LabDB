<?php

namespace MpiCbg\FliesBundle\Wrapper\Barcode;

use MpiCbg\FliesBundle\Wrapper\Entity\ManagedEntity;

class CultureBottle extends ManagedEntity {
    
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
            $parent = $this->em->find('MpiCbgFliesBundle:CultureBottle', $id);
            if ($parent != null) {
                $this->entity->setParent($parent);
            }
        }
    }
}