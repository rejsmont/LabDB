<?php

/*
 * Copyright 2014 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\CoreBundle\Doctrine;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Doctrine Object Manager registry
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("vib.doctrine.registry")
 */
class ObjectManagerRegistry
{
    /**
     * @var Doctrine\Common\Persistence\ManagerRegistry
     */
    protected $doctrineManagerRegistry;
    
    /**
     * @var array
     */
    private $managers;

    
    /**
     * Construct ObjectManagerRegistry
     * 
     * @DI\InjectParams({
     *     "managerRegistry" = @DI\Inject("doctrine"),
     * })
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry  $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managers = array();
        $this->doctrineManagerRegistry = $managerRegistry;
    }

    /**
     * Get manager for class
     * 
     * @return VIB\CoreBundle\Doctrine\ObjectManager
     */
    public function getManagerForClass($object = null)
    {
        if (null == $object) {
            
            return $this->doctrineManagerRegistry->getManager();
        }
        
        $class = is_object($object) ? get_class($object) : $object;
        
        $managers = array();
                
        foreach ($this->managers as $manager) {
            $managed_class = $manager->getManagedClass();
            $priority = $this->getAncestorDepth($class, $managed_class);
            
            if (false !== $priority) {
                $managers[$priority] = $manager;
            }
        }
        
        if (count($managers)) {
            
            return $managers[min(array_keys($managers))];
        }
        
        return $this->doctrineManagerRegistry->getManagerForClass($class);
    }
    
    /**
     * Add ObjectManager to the registry
     * 
     * @param VIB\CoreBundle\Doctrine\ObjectManager $manager
     */
    public function addManager(ObjectManager $manager)
    {
        $this->managers[] = $manager;
    }
    
    /**
     * Get number of classes between $class and $ancestor
     * 
     * @param string $class
     * @param string $ancestor
     * @return int|false
     */
    private function getAncestorDepth($class, $ancestor)
    {
        if ($class == $ancestor) {
            return 0;
        } else {
            if ((false === ($parent = get_parent_class($class)))||
                (false === ($depth = $this->getAncestorDepth($parent, $ancestor)))) {
                return false;
            } else {
                return 1 + $depth;
            }
        }
    }
}
