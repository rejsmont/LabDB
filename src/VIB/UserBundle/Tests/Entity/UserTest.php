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

namespace VIB\UserBundle\Tests\Entity;

use VIB\UserBundle\Entity\User;


class UserTest extends \PHPUnit_Framework_TestCase
{
    private $user;
    
    public function testGetSetGivenName()
    {
        $this->user->setGivenName('John');
        $this->assertEquals('John',$this->user->getGivenName());
    }
    
    public function testGetSetSurname()
    {
        $this->user->setSurname('Doe');
        $this->assertEquals('Doe',$this->user->getSurname());
    }
    
    public function testGetInitials()
    {
        $this->user->setGivenName('John Michael');
        $this->assertEquals('JM',$this->user->getInitials());
    }
    
    public function testGetFullName()
    {
        $this->user->setGivenName('John Michael');
        $this->user->setSurname('Doe');
        $this->assertEquals('John Michael Doe',$this->user->getFullName());
    }
    
    public function testGetFullNameNoName()
    {
        $this->user->setUsername('Test');
        $this->assertEquals('Test',$this->user->getFullName());
    }
    
    public function testGetShortNameNoName()
    {
        $this->user->setUsername('Test');
        $this->assertEquals('Test',$this->user->getShortName());
    }
    
    public function testToStringLongName()
    {
        $this->user->setGivenName('John Michael');
        $this->user->setSurname('Doe');
        $this->assertEquals('JM Doe',(string) $this->user);
    }
    
    public function testToStringShortName()
    {
        $this->user->setGivenName('John');
        $this->user->setSurname('Doe');
        $this->assertEquals('John Doe',(string) $this->user);
    }
    
    protected function setUp()
    {
        $this->user = new User();
    }
}
