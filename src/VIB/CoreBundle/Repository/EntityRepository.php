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

use JMS\DiExtraBundle\Annotation as DI;
use VIB\CoreBundle\Doctrine\ObjectManager;
use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @var VIB\SecurityBundle\Bridge\Doctrine\AclFilter $_aclFilter
     */
    protected $_aclFilter;

    /**
     * @var VIB\CoreBundle\Doctrine\ObjectManager $_objectManager
     */
    protected $_objectManager;
    
    
    /**
     * Set the ACL filter service
     *
     * @DI\InjectParams({ "aclFilter" = @DI\Inject("vib.security.filter.acl") })
     * 
     * @param VIB\SecurityBundle\Bridge\Doctrine\AclFilter $aclFilter
     */
    public function setAclFilter(AclFilter $aclFilter)
    {
        $this->_aclFilter = $aclFilter;
    }
    
    /**
     * Return the ACL filter service
     * 
     * @return VIB\SecurityBundle\Bridge\Doctrine\AclFilter
     */
    protected function getAclFilter()
    {
        return $this->_aclFilter;
    }
    
    /**
     * Set the Object manager service
     * 
     * @DI\InjectParams({ "objectManager" = @DI\Inject("vib.doctrine.manager") })
     * 
     * @param VIB\CoreBundle\Doctrine\ObjectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }
    
    /**
     * Get the Object manager service
     * 
     * @return type VIB\CoreBundle\Doctrine\ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->_objectManager;
    }
    
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
     * @return Doctrine\ORM\Query
     */
    public function getListQuery($options = array())
    {
        $qb = $this->getListQueryBuilder($options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : array();
        $user = isset($options['user']) ? $options['user'] : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);
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
     * @return Doctrine\ORM\Query
     */
    public function getCountQuery($options = array())
    {
        $qb = $this->getCountQueryBuilder($options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : null;
        $user = isset($options['user']) ? $options['user'] : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->getAclFilter()->apply($qb, $permissions, $user);
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
     * Get a single Entity by its id
     * 
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getEntity($id, $options = array())
    {
        return $this->getEntityQueryBuilder($id, $options)->getQuery()->getSingleResult();
    }

    /**
     * Get Entity Query Builder
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
}
