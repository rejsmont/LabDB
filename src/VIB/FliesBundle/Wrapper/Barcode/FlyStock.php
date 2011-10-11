<?php

namespace VIB\FliesBundle\Wrapper\Barcode;

use VIB\FliesBundle\Wrapper\Entity\ManagedEntity;

class FlyStock extends ManagedEntity {
    
    private $sourceCrossBarcode;
    
    public function getSourceCrossBarcode()
    {
        if ($this->entity->getSourceCross() != null) {
            if ($this->entity->getSourceCross()->getBottle() != null) {
                return $this->entity->getSourceCross()->getBottle()->getId();
            }
        }
    }
    
    public function setSourceCrossBarcode($id)
    {
        $this->sourceCrossBarcode = $id;
        
        if ($id !== null) {
            $sourceCrossBottle = $this->em->find('VIBFliesBundle:FlyVial', $id);
            if ($sourceCrossBottle != null) {
                $sourceCross = $sourceCrossBottle->getCross();
                if ($sourceCross != null)
                $this->entity->setSourceCross($sourceCross);
            }
        }
    }
}