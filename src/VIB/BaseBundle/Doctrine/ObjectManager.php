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

namespace VIB\BaseBundle\Doctrine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManagerDecorator;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * ACL aware implementation of Doctrine\Common\Persistence\ObjectManagerDecorator
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ObjectManager extends ObjectManagerDecorator {

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;
    
    /**
     * @var \Symfony\Component\Security\Acl\Model\AclProviderInterface
     */
    protected $aclProvider;
    
    
    /**
     * Construct ObjectManager
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry $mr
     * @param Symfony\Component\Security\Core\User\UserProviderInterface $userManager
     * @param Symfony\Component\Security\Acl\Model\AclProviderInterface $aclProvider
     */
    public function __construct(ManagerRegistry $mr,
                                UserProviderInterface $userProvider,
                                MutableAclProviderInterface $aclProvider) {
        $this->wrapped = $mr->getManager();
        $this->userProvider = $userProvider;
        $this->aclProvider = $aclProvider;
    }
    
    /**
     * Create ACL for object(s)
     * 
     * @param object $objects
     * @param array $acl
     */
    public function createACL($objects, array $acl_array) {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->createACL($object, $acl_array);
            }
        } else {
            $objectIdentity = ObjectIdentity::fromDomainObject($objects);
            $aclProvider = $this->aclProvider;
            $acl = $aclProvider->createAcl($objectIdentity);
            foreach ($acl_array as $acl_entry) {
                $identity = $acl_entry['identity'];
                $permission = $acl_entry['permission'];
                if ($identity instanceof UserInterface) {
                    $identity = UserSecurityIdentity::fromAccount($identity);
                } elseif (is_string($identity)) {
                    $identity = new RoleSecurityIdentity($identity);
                }
                $acl->insertObjectAce($identity, $permission);
            }
            $aclProvider->updateAcl($acl);
        }
    }
    
    /**
     * Get object's owner
     * 
     * @param object $object
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwner($object) {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $aclProvider = $this->aclProvider;
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            foreach($acl->getObjectAces() as $ace) {
                if ($ace->getMask() == MaskBuilder::MASK_OWNER) {
                    $securityIdentity = $ace->getSecurityIdentity();
                    if ($securityIdentity instanceof UserSecurityIdentity) {
                        $userProvider = $this->userProvider;
                        return $userProvider->loadUserByUsername($securityIdentity->getUsername());
                    }
                }
            }
        } catch (AclNotFoundException $e) {
            return null;
        }
        return null;
    }
}

?>
