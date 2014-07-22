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

namespace VIB\CoreBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\ORM\Event\LifecycleEventArgs;

use VIB\CoreBundle\Doctrine\ObjectManager;
use VIB\CoreBundle\Doctrine\ObjectManagerRegistry;
use VIB\CoreBundle\Entity\SecuredEntityInterface;

/**
 * Description of DoctrineAclListener
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\DoctrineListener(
 *     events = {"preRemove", "postPersist"}
 * )
 */
class AclDoctrineListener {
    
    /**
     * @var VIB\CoreBundle\Doctrine\ObjectManagerRegistry
     */
    protected $registry;
    
    
    /**
     * Construct AclDoctrineListener
     * 
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("vib.doctrine.registry"),
     * })
     * 
     * @param VIB\CoreBundle\Doctrine\ObjectManagerRegistry  $registry
     */
    public function __construct(ObjectManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * 
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        
        if ($object instanceof SecuredEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof ObjectManager)&&($om->isAutoAclEnabled())) {
                $om->removeACL($object);
            }
        }
    }
    
    /**
     * 
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof SecuredEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof ObjectManager)&&($om->isAutoAclEnabled())) {
                $om->createACL($object);
            }
        }
    }
}
