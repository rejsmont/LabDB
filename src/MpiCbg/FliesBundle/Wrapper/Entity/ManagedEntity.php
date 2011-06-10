<?php

namespace MpiCbg\FliesBundle\Wrapper\Entity;

class ManagedEntity {
    
    protected $em;
    
    protected $entity;
    
    public function __construct($em, $entity) {
        $this->em = $em;
        $this->entity = $entity;
    }
    
    public function getEntity() {
        return $this->entity;
    }
}

?>