<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\FliesBundle\Repository;

use VIB\CoreBundle\Repository\EntityRepository;

/**
 * RackRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RackRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getEntityQueryBuilder($id, $options = array())
    {
        return $this->createQueryBuilder('e')
                ->addSelect('o')
                ->addSelect('c')
                ->addSelect('i')
                ->leftJoin('e.positions', 'o')
                ->leftJoin('o.contents', 'c')
                ->leftJoin('e.incubator', 'i')
                ->where('e.id = :id')
                ->setParameter('id', $id);
    }
    
    /**
     * 
     * @return type
     */
    public function getRacksWithMyVialsQueryBuilder()
    {
        return $this->getListQueryBuilder()
                ->addSelect('p')
                ->addSelect('v')
                ->join('e.positions', 'p')
                ->join('p.contents', 'v')
                ->orderBy('e.id');
    }
    
    /**
     * 
     * @return type
     */
    public function getRacksWithMyVialsQuery()
    {
        $qb = $this->getRacksWithMyVialsQueryBuilder();
        $permissions = array('OWNER');
        $user = null;
        
        return $this->getAclFilter()->apply($qb, $permissions, $user, 'v');
    }
    
    /**
     * 
     * @return type
     */
    public function getRacksWithMyVials()
    {
        return $this->getRacksWithMyVialsQuery()->getResult();
    }
}
