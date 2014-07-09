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

namespace VIB\CoreBundle\Repository;

use VIB\CoreBundle\Filter\ListFilterInterface;
use VIB\CoreBundle\Filter\EntityFilterInterface;
use VIB\CoreBundle\Filter\SecureFilterInterface;

use VIB\CoreBundle\Doctrine\ObjectManager;
use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;

/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class NewEntityRepository extends EntityRepository
{
    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\Common\Collections\Collection
     */
    public function getList(ListFilterInterface $filter = null)
    {
        return $this->getListQuery($filter)->getResult();
    }

    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\Query
     */
    public function getListQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getListQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }

        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);;
    }

    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e');
    }

    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return integer
     */
    public function getListCount(ListFilterInterface $filter = null)
    {
        return $this->getCountQuery($filter)->getSingleScalarResult();
    }

    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\Query
     */
    public function getCountQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getCountQueryBuilder($filter);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }
        
        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);
    }

    /**
     *
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getCountQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->select('count(e.id)');
    }

    /**
     * Get a single Entity by its id
     * 
     * @param  VIB\CoreBundle\Filter\EntityFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getEntity($id, EntityFilterInterface $filter = null)
    {
        return $this->getEntityQueryBuilder($id, $filter)->getQuery()->getSingleResult();
    }

    /**
     * Get Entity Query Builder
     * 
     * @param  VIB\CoreBundle\Filter\EntityFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getEntityQueryBuilder($id, EntityFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $id);
    }
}
