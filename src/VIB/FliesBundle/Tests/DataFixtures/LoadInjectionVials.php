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

namespace VIB\FliesBundle\Tests\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use VIB\FliesBundle\Entity\InjectionVial;

class LoadInjectionVials extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $manager = $this->container->get('vib.doctrine.registry')->getManagerForClass('VIB\FliesBundle\Entity\InjectionVial');

        $user_acl = array(
            array('identity' => $this->getReference('user'),
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_USER',
                  'permission' => MaskBuilder::MASK_VIEW)
        );

        $vial_1 = new InjectionVial();
        $vial_1->setTargetStock($this->getReference('stock_4'));
        $vial_1->setConstructName('test');
        $vial_1->setEmbryoCount(100);
        $manager->persist($vial_1);
        $manager->flush();
        $manager->createACL($vial_1, $user_acl);

        $vial_2 = new InjectionVial();
        $vial_2->setTargetStockVial($this->getReference('vial_1'));
        $vial_2->setConstructName('test');
        $vial_2->setEmbryoCount(100);
        $manager->persist($vial_2);
        $manager->flush();
        $manager->createACL($vial_2, $user_acl);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7;
    }
}
