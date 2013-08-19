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

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;

/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @var $aclFilter VIB\SecurityBundle\Bridge\Doctrine\AclFilter
     */
    protected $aclFilter;

    /**
     *
     * @param  array                                  $options
     * @return Doctrine\Common\Collections\Collection
     */
    public function getList($options = array())
    {
        return $this->getListQuery($options)->getResult();
    }

    /**
     *
     * @param  array              $options
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery($options = array())
    {
        $qb = $this->getListQueryBuilder($options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : array();
        $user = isset($options['user']) ? $options['user'] : null;
        if (false === $permissions) {
            return $qb->getQuery();
        } else {
            return $this->aclFilter->apply($qb, $permissions, $user);
        }
    }

    /**
     *
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getListQueryBuilder($options = array())
    {
        return $this->createQueryBuilder('e');
    }

    /**
     *
     * @param  array   $options
     * @return integer
     */
    public function getListCount($options = array())
    {
        return $this->getCountQuery($options)->getSingleScalarResult();
    }

    /**
     *
     * @param  array   $options
     * @return integer
     */
    public function getCountQuery($options = array())
    {
        $qb = $this->getCountQueryBuilder($options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : null;
        $user = isset($options['user']) ? $options['user'] : null;
        if (false === $permissions) {
            return $qb->getQuery();
        } else {
            return $this->aclFilter->apply($qb, $permissions, $user);
        }
    }

    /**
     *
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getCountQueryBuilder($options = array())
    {
        return $this->createQueryBuilder('e')
                ->select('count(e.id)');
    }

    /**
     *
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getEntity($id, $options = array())
    {
        return $this->getEntityQueryBuilder($id, $options)->getQuery()->getSingleResult();
    }

    /**
     *
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getEntityQueryBuilder($id, $options = array())
    {
        return $this->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $id);
    }

    /**
     * Set the ACL filter service
     *
     * @param VIB\SecurityBundle\Bridge\Doctrine\AclFilter
     */
    public function setAclFilter(AclFilter $aclFilter)
    {
        $this->aclFilter = $aclFilter;
    }
}
