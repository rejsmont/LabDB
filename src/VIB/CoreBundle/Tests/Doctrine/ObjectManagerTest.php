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

namespace VIB\CoreBundle\Tests\Doctrine;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\CoreBundle\Doctrine\ObjectManager;
use VIB\CoreBundle\Entity\Entity;

class ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    private $om;
    private $aclProvider;
    private $userProvider;

    public function testCreateACL()
    {
        $user = new FakeUser();
        $acl = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclInterface');

        $acl->expects($this->at(0))
            ->method('insertObjectAce')
            ->with(UserSecurityIdentity::fromAccount($user),MaskBuilder::MASK_OWNER);
        $acl->expects($this->at(1))
            ->method('insertObjectAce')
            ->with(new RoleSecurityIdentity('ROLE_TEST'),MaskBuilder::MASK_VIEW);

        $this->aclProvider->expects($this->once())
            ->method('createACL')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($acl));
        $this->aclProvider->expects($this->once())->method('updateAcl')->with($acl);

        $this->om->createACL(new FakeEntity(), array(
            array('identity' => $user,
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_TEST',
                  'permission' => MaskBuilder::MASK_VIEW)));
    }

    public function testCreateACLCollection()
    {
        $user = new FakeUser();
        $entity = new FakeEntity();
        $collection = new ArrayCollection();
        $collection->add($entity);

        $acl = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclInterface');

        $acl->expects($this->at(0))
            ->method('insertObjectAce')
            ->with(UserSecurityIdentity::fromAccount($user),MaskBuilder::MASK_OWNER);
        $acl->expects($this->at(1))
            ->method('insertObjectAce')
            ->with(new RoleSecurityIdentity('ROLE_TEST'),MaskBuilder::MASK_VIEW);

        $this->aclProvider->expects($this->once())
            ->method('createACL')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($acl));
        $this->aclProvider->expects($this->once())->method('updateAcl')->with($acl);

        $this->om->createACL($collection, array(
            array('identity' => $user,
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_TEST',
                  'permission' => MaskBuilder::MASK_VIEW)));
    }

    public function testGetOwner()
    {
        $user = new FakeUser();

        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($this->getFakeObjectAces()));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->returnValue($user));

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals($user, $owner);
    }

    public function testGetOwnerNoAcl()
    {
        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->throwException(new AclNotFoundException()));

        $this->userProvider->expects($this->never())
            ->method('loadUserByUsername');

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals(null, $owner);
    }

    public function testGetOwnerNoUser()
    {
        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($this->getFakeObjectAces()));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->throwException(new UsernameNotFoundException()));

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals(null, $owner);
    }

    protected function setUp()
    {
        $mr = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');

        $this->aclProvider = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface');
        $this->userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->aclFilter = $this->getMockBuilder('VIB\SecurityBundle\Bridge\Doctrine\AclFilter')
                                 ->disableOriginalConstructor()->getMock();

        $this->om = new ObjectManager($mr, $this->userProvider, $this->aclProvider, $this->aclFilter);

    }

    private function getFakeObjectAces()
    {
        $aces = array();

        $aclEntry_1 = $this->getMock('Symfony\Component\Security\Acl\Model\EntryInterface');
        $aclEntry_1->expects($this->once())
             ->method('getMask')
             ->will($this->returnValue(MaskBuilder::MASK_VIEW));
        $aclEntry_1->expects($this->never())
             ->method('getSecurityIdentity');
        $aces[] = $aclEntry_1;

        $aclEntry_2 = $this->getMock('Symfony\Component\Security\Acl\Model\EntryInterface');
        $aclEntry_2->expects($this->once())
             ->method('getMask')
             ->will($this->returnValue(MaskBuilder::MASK_OWNER));
        $aclEntry_2->expects($this->once())
             ->method('getSecurityIdentity')
             ->will($this->returnValue(
                    new UserSecurityIdentity('user','VIB\CoreBundle\Tests\Doctrine\FakeUser')));
        $aces[] = $aclEntry_2;

        $aclInterface = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface');
        $aclInterface->expects($this->once())
             ->method('getObjectAces')
             ->will($this->returnValue($aces));

        return $aclInterface;
    }

}

class FakeUser implements UserInterface
{
    public function eraseCredentials()
    {

    }

    public function getPassword()
    {
        return 'password';
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        return 'pepper';
    }

    public function getUsername()
    {
        return 'user';
    }
}

class FakeEntity extends Entity
{
    public function getId()
    {
        return rand();
    }
}
