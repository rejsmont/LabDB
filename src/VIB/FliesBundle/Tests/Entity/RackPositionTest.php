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

namespace VIB\FliesBundle\Tests\Entity;

use VIB\FliesBundle\Entity\Rack;
use VIB\FliesBundle\Entity\Vial;

class RackPositionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positionProvider
     */
    public function testConstruct($position)
    {
        $this->assertInstanceOf('VIB\FliesBundle\Entity\Rack', $position->getRack());
        $this->assertEquals('A', $position->getRow());
        $this->assertEquals(2, $position->getColumn());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testToString($position)
    {
        $this->assertEquals('R000000 A02', (string) $position);
    }

    /**
     * @dataProvider positionProvider
     */
    public function testRow($position)
    {
        $this->assertEquals('A', $position->getRow());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testColumn($position)
    {
        $this->assertEquals(2, $position->getColumn());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testIsAt($position)
    {
        $this->assertTrue($position->isAt(1,2));
        $this->assertFalse($position->isAt(2,2));
    }

    /**
     * @dataProvider positionProvider
     */
    public function testRack($position)
    {
        $this->assertInstanceOf('VIB\FliesBundle\Entity\Rack', $position->getRack());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testContent($position)
    {
        $this->assertNull($position->getContent());
        $vial = new Vial();
        $position->setContent($vial);
        $this->assertEquals($vial, $position->getContent());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testIsEmpty($position)
    {
        $this->assertTrue($position->isEmpty());
        $vial = new Vial();
        $position->setContent($vial);
        $this->assertFalse($position->isEmpty());
    }

    /**
     * @dataProvider positionProvider
     */
    public function testSetPreviousContent($position)
    {
        $vial = new Vial();
        $position->setPreviousContent($vial);
        $this->assertEquals($position, $vial->getPreviousPosition());
    }

    public function positionProvider()
    {
        $rack = new Rack(3, 3);

        return array(array($rack->getPosition(1, 2)));
    }
}
