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

namespace VIB\UserBundle\Tests\EventListener;

use Symfony\Component\Security\Http\SecurityEvents;
use VIB\UserBundle\EventListener\ShibbolethLoginListener;

class ShibbolethLoginListenerTest extends \PHPUnit_Framework_TestCase
{
    private $listener;
    private $userProvider;

    public function testGetSubscribedEvents()
    {
        $result = $this->listener->getSubscribedEvents();
        $this->assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, $result);
        $this->assertEquals('onInteractiveLogin', $result[SecurityEvents::INTERACTIVE_LOGIN]);
    }

    public function testOnInteractiveLogin()
    {
        $token = $this->getMockBuilder('KULeuven\ShibbolethBundle\Security\ShibbolethUserToken')
            ->disableOriginalConstructor()->getMock();
        $event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')
            ->disableOriginalConstructor()->getMock();
        $event->expects($this->once())
            ->method('getAuthenticationToken')
            ->will($this->returnValue($token));
        $this->userProvider->expects($this->once())->method('updateUser');
        $this->listener->onInteractiveLogin($event);
    }

    public function testOnInteractiveLoginIgnoreToken()
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');;
        $event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')
            ->disableOriginalConstructor()->getMock();
        $event->expects($this->once())
            ->method('getAuthenticationToken')
            ->will($this->returnValue($token));
        $this->userProvider->expects($this->never())->method('updateUser');
        $this->listener->onInteractiveLogin($event);
    }

    protected function setUp()
    {
        $this->userProvider = $this->getMockBuilder('VIB\UserBundle\Security\ShibbolethUserProvider')
            ->disableOriginalConstructor()->getMock();
        $this->listener = new ShibbolethLoginListener($this->userProvider);
    }
}
