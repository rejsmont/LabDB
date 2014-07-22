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

namespace VIB\FliesBundle\Tests\Doctrine;

use VIB\FliesBundle\Doctrine\VialManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\Incubator;

class VialManagerTest extends \PHPUnit_Framework_TestCase
{
    private $om;
    private $aclProvider;
    private $userProvider;
    private $entityManager;

    /**
     * @dataProvider flipProvider
     */
    public function testFlip($vials, $vial, $setSource, $trashSource)
    {
        $vial->setTrashed(false);
        $this->entityManager->expects($this->exactly($trashSource ? 2 : 1))->method('persist');
        $newVials = $this->om->flip($vials, $setSource, $trashSource);
        if (($newVial = $newVials) instanceof Collection) {
            foreach ($newVials as $newVial) {
                $this->assertEquals($setSource ? $vial : null, $newVial->getParent());
            }
        } else {
            $this->assertEquals($setSource ? $vial : null, $newVial->getParent());
        }
        $this->assertEquals($trashSource, $vial->isTrashed());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testTrashUntrash($vials, $vial)
    {
        $this->entityManager->expects($this->exactly(2))->method('persist')->with($vial);
        $this->om->trash($vials);
        $this->assertEquals(true,$vial->isTrashed());
        $this->om->untrash($vials);
        $this->assertEquals(false,$vial->isTrashed());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testMarkPrinted($vials, $vial)
    {
        $this->entityManager->expects($this->once())->method('persist')->with($vial);
        $this->om->markPrinted($vials);
        $this->assertEquals(true,$vial->isLabelPrinted());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testIncubate($vials, $vial)
    {
        $incubator = new Incubator();
        $this->entityManager->expects($this->once())->method('persist')->with($vial);
        $this->om->incubate($vials,$incubator);
        $this->assertEquals($incubator,$vial->getStorageUnit());
    }

    /**
     * @dataProvider expandProvider
     */
    public function testExpand($vial, $count, $setSource, $size)
    {
        $this->entityManager->expects($this->exactly((null === $size) ? $count : $count * 2))->method('persist');
        $newVials = $this->om->expand($vial, $count, $setSource, $size);
        foreach ($newVials as $newVial) {
            $this->assertEquals($setSource ? $vial : null, $newVial->getParent());
            $this->assertEquals((null === $size) ? 'medium' : $size, $newVial->getSize());
        }
    }

    protected function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $mr = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $mr->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->entityManager));
        $this->aclProvider = $this->getMock('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface');
        $this->userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        
        $this->om = new VialManager($mr, $this->userProvider, $this->aclProvider);

    }

    public function vialProvider()
    {
        $vial = new Vial();
        $collection = new ArrayCollection();
        $collection->add($vial);

        return array(
          array($vial, $vial),
          array($collection, $vial),
        );
    }

    public function expandProvider()
    {
        $vial = new Vial();

        return array(
          array($vial, 1, true, null),
          array($vial, 2, false, null),
          array($vial, 1, true, 'large'),
          array($vial, 2, false, 'large'),
        );
    }

    public function flipProvider()
    {
        $vial = new Vial();
        $collection = new ArrayCollection();
        $collection->add($vial);

        return array(
          array($vial, $vial, true, false),
          array($vial, $vial, true, true),
          array($vial, $vial, false, true),
          array($vial, $vial, false, false),
          array($collection, $vial, true, false),
          array($collection, $vial, true, true),
          array($collection, $vial, false, true),
          array($collection, $vial, false, false),
        );
    }
}
