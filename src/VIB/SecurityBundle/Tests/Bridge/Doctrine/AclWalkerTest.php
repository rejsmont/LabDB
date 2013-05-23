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

use VIB\SecurityBundle\Bridge\Doctrine\AclWalker;

class AclWalkerTest extends \PHPUnit_Framework_TestCase
{
    private $walker;
    private $query;

    public function testWalkFromClause()
    {
        $fromClause = $this->getMockBuilder('Doctrine\ORM\Query\AST\FromClause')
            ->disableOriginalConstructor()->getMock();
        $result = $this->walker->walkFromClause($fromClause);
        $this->assertContains('FROM', $result);
    }

    protected function setUp()
    {
        $conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()->getMock();
        $conf = $this->getMockBuilder('Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('getConnection')
            ->will($this->returnValue($conn));
        $em->expects($this->once())->method('getConfiguration')
            ->will($this->returnValue($conf));
        $this->query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->setMethods(array('getEntityManager'))
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->query->expects($this->once())->method('getEntityManager')
            ->will($this->returnValue($em));
        $parserResult = $this->getMockBuilder('Doctrine\ORM\Query\ParserResult')
                ->disableOriginalConstructor()->getMock();
        $this->walker = new AclWalker($this->query, $parserResult, array());
    }
}
