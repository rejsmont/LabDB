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

namespace VIB\SecurityBundle\Tests\Bridge\Doctrine;

use VIB\SecurityBundle\Bridge\Doctrine\AclHelper;


class AclHelperTest extends \PHPUnit_Framework_TestCase
{
    private $helper;
    
    public function testApply()
    {
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->setMethods(array('getParameters'))
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $query->expects($this->once())->method('getParameters')
            ->will($this->returnValue(array()));
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->once())->method('getDQLPart')
            ->with('where')
            ->will($this->returnValue('somewhere'));
        $queryBuilder->expects($this->once())->method('getQuery')
            ->will($this->returnValue($query));
        $queryBuilder->expects($this->once())->method('getRootEntities')
            ->will($this->returnValue(array('TestClass')));
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->once())->method('getRoles')
            ->will($this->returnValue(array()));
        $resultQuery = $this->helper->apply($queryBuilder, array('VIEW'), $user);
        $sql = $resultQuery->getHint('acl.extra.query');
        $this->assertContains('WHERE  c.class_type IN ("TestClass")', $sql);
        $this->assertContains('AND s.identifier IN ("Mock_UserInterface', $sql);
    }
    
    protected function setUp()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()->getMock();
        $entityManager->expects($this->once())->method('getClassMetadata')
            ->will($this->returnValue($metadata));
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()->getMock();
        $connection->expects($this->atLeastOnce())->method('getDatabasePlatform')
            ->will($this->returnValue($this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform')));
        $entityManager->expects($this->once())->method('getConnection')
            ->will($this->returnValue($connection));
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $doctrine = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $doctrine->expects($this->once())->method('getManager')
            ->will($this->returnValue($entityManager));
        $doctrine->expects($this->once())->method('getConnection')
            ->will($this->returnValue($connection));
        $options = array(0 => null, 1 => array(), 2 => null);
        $this->helper = new AclHelper($doctrine, $context, $options);
    }
}
