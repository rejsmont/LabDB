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

namespace VIB\UserBundle\Tests\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use VIB\UserBundle\Entity\User;


class LoadUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager = $this->container->get('fos_user.user_manager');
        
        $user = new User;
        $user->setUsername('jdoe');
        $user->setPlainPassword('password');
        $user->setGivenName('John');
        $user->setSurname('Doe');
        $user->setEmail('jdoe@test.net');
        $user->addRole('ROLE_USER');
        $user->setEnabled(true);
        $manager->updateUser($user);
        $this->addReference('user', $user);
        
        $admin = new User;
        $admin->setUsername('asmith');
        $admin->setPlainPassword('password');
        $admin->setGivenName('Adam');
        $admin->setSurname('Smith');
        $admin->setEmail('asmith@test.net');
        $admin->addRole('ROLE_USER');
        $admin->addRole('ROLE_ADMIN');
        $admin->setEnabled(true);
        $manager->updateUser($admin);
        $this->addReference('admin', $admin);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
