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

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Doctrine\CrossVialManager;
use VIB\FliesBundle\Entity\CrossVial;

class CrossVialManagerTest extends \PHPUnit_Framework_TestCase
{
    private $om;
    private $aclProvider;
    private $userProvider;
    private $entityManager;

    /**
     * @dataProvider crossProvider
     */
    public function testMarkSterile($crosses, $cross)
    {
        $this->entityManager->expects($this->once())->method('persist')->with($cross);
        $this->om->markSterile($crosses);
        $this->assertEquals('sterile',$cross->getOutcome());
    }

    /**
     * @dataProvider crossProvider
     */
    public function testMarkSuccessful($crosses, $cross)
    {
        $this->entityManager->expects($this->once())->method('persist')->with($cross);
        $this->om->markSuccessful($crosses);
        $this->assertEquals('successful',$cross->getOutcome());
    }

    /**
     * @dataProvider crossProvider
     */
    public function testMarkFailed($crosses, $cross)
    {
        $this->entityManager->expects($this->once())->method('persist')->with($cross);
        $this->om->markFailed($crosses);
        $this->assertEquals('failed',$cross->getOutcome());
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
        
        $this->om = new CrossVialManager($mr, $this->userProvider, $this->aclProvider);

    }

    public function crossProvider()
    {
        $cross = new CrossVial();

        $collection = new ArrayCollection();
        $collection->add($cross);

        return array(
          array($cross, $cross),
          array($collection, $cross),
        );
    }
}
