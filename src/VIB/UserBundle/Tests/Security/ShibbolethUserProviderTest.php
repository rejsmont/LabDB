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

/*
namespace VIB\UserBundle\Tests\Security;

use VIB\UserBundle\Security\ShibbolethUserProvider;

class ShibbolethUserProviderTest extends \PHPUnit_Framework_TestCase
{
    private $userProvider;
    private $userManager;
    private $userToken;

    public function testCreateUser()
    {
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue($this->getMock('FOS\UserBundle\Model\User')));
        $user = $this->userProvider->createUser($this->userToken);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);
    }

    public function testUpdateUser()
    {
        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue($this->getMock('FOS\UserBundle\Model\User')));
        $user = $this->userProvider->updateUser($this->userToken);
        $this->assertInstanceOf('FOS\UserBundle\Model\User', $user);
    }

    public function testUpdateUserNotFound()
    {
        $this->userManager->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null));
        $this->setExpectedException('Symfony\Component\Security\Core\Exception\UsernameNotFoundException');
        $this->userProvider->updateUser($this->userToken);
    }

    protected function setUp()
    {
        $this->userManager = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $this->userToken = $this->getMock('KULeuven\ShibbolethBundle\Security\ShibbolethUserToken');
        $this->userProvider = new ShibbolethUserProvider($this->userManager);
    }
}
*/
