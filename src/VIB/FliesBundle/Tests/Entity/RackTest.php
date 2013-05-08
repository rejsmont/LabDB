<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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
use VIB\FliesBundle\Entity\Incubator;


class RackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider rackProvider
     */
    public function testConstruct($rack)
    {
        $this->assertEquals(5,$rack->getRows());
        $this->assertEquals(3,$rack->getColumns());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testGetLabelBarcode($rack)
    {
        $this->assertEquals('R000001',$rack->getLabelBarcode());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testGetLabelText($rack)
    {
        $this->assertEquals('test',$rack->getLabelText());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testToString($rack)
    {
        $this->assertEquals('R000001',$rack->getLabelBarcode());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testDescription($rack)
    {
        $this->assertEquals('test',$rack->getDescription());
        $rack->setDescription('another test');
        $this->assertEquals('another test',$rack->getDescription());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testGetPosition($rack)
    {
        for ($i = 'A', $k = 1; $i <= 'E'; $i++, $k++) {
            for ($j = 1; $j <= 3; $j++) {
                $position = $rack->getPosition($i, $j);
                $numPosition = $rack->getPosition($k, $j);
                $this->assertEquals($i, $position->getRow());
                $this->assertEquals($j, $position->getColumn());
                $this->assertEquals($position, $numPosition);
            }
        }
    }

    /**
     * @dataProvider rackProvider
     */
    public function testGeometry($rack)
    {
        $this->assertEquals('5 ✕ 3', $rack->getGeometry());
        $rack->setGeometry(3, 5);
        $this->assertEquals(3,$rack->getRows());
        $this->assertEquals(5,$rack->getColumns());
        $this->assertEquals('3 ✕ 5', $rack->getGeometry());
    }

    /**
     * @dataProvider rackProvider
     */
    public function testVials($rack)
    {
        $vial_1 = new Vial();
        $vial_2 = new Vial();
        $vial_3 = new Vial();
        $rack->addVial($vial_1, 2, 2);
        $this->assertEquals($vial_1, $rack->getVial(2, 2));
        $rack->addVial($vial_2, 3, 3);
        $this->assertContains($vial_2, $rack->getVials());
        $this->assertEquals(2, count($rack->getVials()));
        $rack->replaceVial(2, 2, $vial_3);
        $this->assertNotContains($vial_1, $rack->getVials());
        $this->assertEquals($vial_3, $rack->getVial(2, 2));
        $rack->removeVial($vial_2);
        $this->assertNotContains($vial_2, $rack->getVials());
        $this->assertEquals(false, $rack->hasVial($vial_2));
        $rack->clearVials();
        $this->assertEquals(0, count($rack->getVials()));
    }

    public function testIncubator()
    {
        $rack = new Rack();
        $incubator = new Incubator();
        $incubator->setTemperature(28);
        $this->assertNull($rack->getIncubator());
        $rack->setIncubator($incubator);
        $this->assertEquals($incubator, $rack->getIncubator());
        
        return $rack;
    }

    /**
     * 
     * @depends testIncubator
     */
    public function testGetTemperature($rack)
    {
        $this->assertEquals(28, $rack->getTemperature());
        $rack->setIncubator(null);
        $this->assertEquals(21, $rack->getTemperature());
    }
    
    public function rackProvider()
    {
        $rack = new FakeRack(5, 3);
        return array(array($rack));
    }
}

class FakeRack extends Rack
{
    public function __construct($rows = null, $columns = null)
    {
        parent::__construct($rows, $columns);
        $this->id = 1;
        $this->description = 'test';
    }
}
