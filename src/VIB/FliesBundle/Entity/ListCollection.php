<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VIB\FliesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\SerializerBundle\Annotation\ExclusionPolicy;
use JMS\SerializerBundle\Annotation\Expose;

use \DateTime;
use \DateInterval;

use VIB\FliesBundle\Entity\FlyStock;
use VIB\FliesBundle\Entity\FlyCross;


/**
 * FlyVial class
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 */
class ListCollection {

    /**
     *
     * @var Doctrine\Common\Collections\Collection
     */
    protected $items;
    
    /**
     * @var string
     */
    protected $action;
    
    /**
     * Construct ListCollection
     *
     * @param VIB\FliesBundle\Entity\FlyVial $parent
     */
    public function __construct($parent = null) {
        $this->items = new ArrayCollection();
    }
    
    /**
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getItems() {
        return $this->items;
    }

    /**
     *
     * @param Doctrine\Common\Collections\Collection $items 
     */
    public function setItems($items) {
        if ($items instanceof Collection)
            $this->items = $items;
        else
            $this->items = new ArrayCollection($items);
    }
    
    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }


}
