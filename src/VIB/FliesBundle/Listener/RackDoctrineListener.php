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

namespace VIB\FliesBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;

use VIB\FliesBundle\Entity\Rack;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * RackDoctrineListener class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\DoctrineListener(
 *     events = {"onFlush"}
 * )
 */
class RackDoctrineListener
{
    /**
     * Persist vials that are stored in the rack
     * 
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );
        
        foreach ($entities as $entity) {
            if ($entity instanceof Rack) {
                foreach ($entity->getContents() as $vial) {
                    $em->persist($vial);
                    $md = $em->getClassMetadata(get_class($vial));
                    $uow->recomputeSingleEntityChangeSet($md, $vial);
                }
            }
        }
    }
}
