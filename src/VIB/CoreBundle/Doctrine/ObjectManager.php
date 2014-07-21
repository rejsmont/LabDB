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

namespace VIB\CoreBundle\Doctrine;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManagerDecorator;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use VIB\CoreBundle\Repository\EntityRepository;
use VIB\CoreBundle\Filter\ListFilterInterface;
use VIB\CoreBundle\Filter\EntityFilterInterface;
use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;

/**
 * ACL aware implementation of Doctrine\Common\Persistence\ObjectManagerDecorator
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("vib.doctrine.manager")
 * @DI\Tag("vibcore.object_manager")
 */
class ObjectManager extends ObjectManagerDecorator
{

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var \Symfony\Component\Security\Acl\Model\MutableAclProviderInterface
     */
    protected $aclProvider;
    
    /**
     * Construct ObjectManager
     *
     * @DI\InjectParams({
     *     "managerRegistry" = @DI\Inject("doctrine"),
     *     "userProvider" = @DI\Inject("user_provider"),
     *     "aclProvider" = @DI\Inject("security.acl.provider")
     * })
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry                $managerRegistry
     * @param Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param Symfony\Component\Security\Acl\Model\AclProviderInterface  $aclProvider
     */
    public function __construct(ManagerRegistry $managerRegistry,
                                UserProviderInterface $userProvider,
                                MutableAclProviderInterface $aclProvider)
    {
        $this->wrapped = $managerRegistry->getManager();
        $this->userProvider = $userProvider;
        $this->aclProvider = $aclProvider;
    }

    /**
     * Get the class this Manager is used for
     * 
     * @return string
     */
    public function getManagedClass()
    {
        return 'VIB\CoreBundle\Entity\Entity';
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($object) {
        $this->removeACL($object);
        parent::remove($object);
    }
    
    /**
     * Create ACL for object(s)
     *
     * @param object $objects
     * @param array  $acl
     */
    public function createACL($objects, array $acl_array)
    {        
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->createACL($object, $acl_array);
            }
        } else {
            $objectIdentity = ObjectIdentity::fromDomainObject($objects);
            $aclProvider = $this->aclProvider;
            $acl = $aclProvider->createAcl($objectIdentity);
            $this->insertAclEntries($acl, $acl_array);
            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * Delete ACL for object(s)
     *
     * @param object $objects
     */
    public function removeACL($objects)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->removeACL($object);
            }
        } else {
            $objectIdentity = ObjectIdentity::fromDomainObject($objects);
            $aclProvider = $this->aclProvider;
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {}
        }
    }
    
    /**
     * Update ACL for object(s)
     *
     * @param object $objects
     */
    public function updateACL($objects, array $acl_array)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->updateACL($object, $acl_array);
            }
        } else {
            $aclProvider = $this->aclProvider;
            $objectIdentity = ObjectIdentity::fromDomainObject($objects);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
                $diff = $this->diffACL($acl, $acl_array);
                $this->updateAclEntries($acl, $diff['update']);
                $this->deleteAclEntries($acl, $diff['delete']);
                $this->insertAclEntries($acl, $diff['insert']);
                $aclProvider->updateAcl($acl);
            } catch (AclNotFoundException $e) {
                $this->createACL($object, $acl_array);
            }
        }
    }
    
    /**
     * Get ACL for object
     *
     * @return array
     */
    public function getACL($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $aclProvider = $this->aclProvider;
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $acl_array = array();
            foreach ($acl->getObjectAces() as $index => $ace) {
                $identity = $this->resolveIdentity($ace);
                $acl_array[$index] = array('identity' => $identity, 'permission' => $ace->getMask());
            }
        } catch (AclNotFoundException $e) {
            
            return array();
        }
        
        return $acl_array;
    }
    
    /**
     * Get object's owner
     *
     * @param  object                                             $object
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwner($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's owner
     *
     * @param  object                                             $object
     * @param  Symfony\Component\Security\Core\User\UserInterface $owner
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function setOwner($objects, $owner)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setOwner($object, $owner);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $owner_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                    $owner_found = true;
                    if ($owner instanceof UserInterface) {
                        $acl_array[$index]['identity'] = $owner;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$owner_found) {
                $acl_array[]= array('identity' => $owner, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }
    
    /**
     * Get object's group
     *
     * @param  object $object
     * @return string
     */
    public function getGroup($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's group
     *
     * @param  object                                             $object
     * @param  string                                             $group
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function setGroup($objects, $group)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setGroup($object, $group);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $group_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                    $group_found = true;
                    if (is_string($group)) {
                        $acl_array[$index]['identity'] = $group;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$group_found) {
                $acl_array[]= array('identity' => $group, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = $this->wrapped->getRepository($className);

        if (! $repository instanceof EntityRepository) {
            throw new \ErrorException('Repository must be an instance of VIB\CoreBundle\Repository\EntityRepository');
        }

        return $repository;
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param string $className The class name of the object to find.
     * @param mixed  $id        The identity of the object to find.
     * @param VIB\CoreBundle\Filter\EntityFilterInterface $filter
     * 
     * @return object The found object.
     */
    public function find($className, $id, $filter = null)
    {
        if (($filter !== null)&&(! $filter instanceof EntityFilterInterface)) {
            throw new \InvalidArgumentException('Argument 3 passed to '
                    . get_class($this) . ' must be an instance of '
                    . 'VIB\CoreBundle\Filter\EntityFilterInterface, '
                    . ((($type = gettype($filter)) == 'object') ? get_class($filter) : $type)
                    . ' given');
        }
        
        $repository = $this->getRepository($className);
        
        return $repository->getEntity($id, $filter);
    }

    /**
     * Finds all entities of the specified type.
     *
     * @param string $className The class name of the objects to find.
     * @param VIB\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return Doctrine\Common\Collections\Collection The entities.
     */
    public function findAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getList($filter);
    }

    /**
     * Counts all entities of the specified type.
     * 
     * @param string $className The class name of the objects to find.
     * @param VIB\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return integer Number of entities.
     */
    public function countAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getListCount($filter);
    }
    
    /**
     * 
     * @param type $ace
     * @return null
     */
    protected function resolveIdentity($ace)
    {
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof UserSecurityIdentity) {
            $userProvider = $this->userProvider;
            try {
                return $userProvider->loadUserByUsername($securityIdentity->getUsername());
            } catch (UsernameNotFoundException $e) {
                return null;
            }
        } elseif ($securityIdentity instanceof RoleSecurityIdentity) {
            return $securityIdentity->getRole();
        }
    }
    
    /**
     * 
     * @param type $acl
     * @param array $acl_array
     * @return array
     */
    protected function diffACL($acl, array $acl_array)
    {
        $insert = $acl_array;
        $update = array();
        $delete = array();
        foreach ($acl->getObjectAces() as $index => $ace) {
            $identity = $this->resolveIdentity($ace);
            $mask = $ace->getMask();
            $found = false;
            foreach ($acl_array as $key => $acl_entry) {
                if ($acl_entry['identity'] == $identity) {
                    $found = true;
                    if ($acl_entry['permission'] != $mask) {
                        $update[$index] = $acl_entry;
                    }
                    unset($insert[$key]);
                }
            }
            if (! $found) {
                $delete[$index] = array('identity' => $identity, 'permission' => $mask);
            }
        }
        
        return array('insert' => $insert, 'update' => $update, 'delete' => $delete);
    }
    
    /**
     * 
     * @param type $acl
     * @param array $update
     */
    protected function updateAclEntries($acl, array $update)
    {
        foreach ($update as $index => $entry) {
            $acl->updateObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * 
     * @param type $acl
     * @param array $delete
     */
    protected function deleteAclEntries($acl, array $delete)
    {
        foreach (array_reverse($delete, true) as $index => $entry) {
            $acl->deleteObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * 
     * @param type $acl
     * @param array $insert
     */
    protected function insertAclEntries($acl, array $insert)
    {
        foreach ($insert as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if ($identity instanceof UserInterface) {
                $identity = UserSecurityIdentity::fromAccount($identity);
            } elseif (is_string($identity)) {
                $identity = new RoleSecurityIdentity($identity);
            }
            $acl->insertObjectAce($identity, $permission);
        }
    }
}
