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

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\CrossVial;
use VIB\FliesBundle\Entity\Rack;
use VIB\FliesBundle\Entity\RackPosition;
use VIB\FliesBundle\Entity\Incubator;


class VialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTime 
     */
    private $date;
    
    /**
     * @dataProvider vialProvider
     */
    public function testConstruct($vial)
    {
        $this->assertEquals(21,$vial->getTemperature());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection',$vial->getChildren());
        $this->assertEquals(0,count($vial->getChildren()));
        $this->assertNull($vial->getParent());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection',$vial->getVirginCrosses());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection',$vial->getMaleCrosses());
        $this->assertFalse($vial->isLabelPrinted());
        $this->assertFalse($vial->isTrashed());
        $this->assertEquals($this->date,$vial->getSetupDate());
        $this->assertNull($vial->getFlipDate());
        $this->assertNull($vial->getNotes());
        $this->assertEquals('medium', $vial->getSize());
        $this->assertNull($vial->getPosition());
        
        return $vial;
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testGetId($vial)
    {
        $this->assertEquals(1,$vial->getId());
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testToString($vial)
    {
        $this->assertSame('000001',(string) $vial);
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testGetLabelBarcode($vial)
    {
        $this->assertSame('000001',$vial->getLabelBarcode());
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testGetLabelText($vial)
    {
        $this->assertSame('1',$vial->getLabelText());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetLabelDate($vial)
    {
        $this->assertEquals($this->date, $vial->getLabelDate());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetName($vial)
    {
        $this->assertSame('1',$vial->getLabelText());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSetupDate($vial)
    {
        $vial->setSetupDate($date = $this->date);
        $this->assertEquals($date, $vial->getSetupDate());
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testFlipDate($vial)
    {
        $vial->setFlipDate($date = $this->date);
        $this->assertEquals($date, $vial->getFlipDate());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testNotes($vial)
    {
        $vial->setNotes($note = 'test');
        $this->assertEquals($note, $vial->getNotes());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSize($vial)
    {
        $vial->setSize($size = 'large');
        $this->assertEquals($size, $vial->getSize());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testChildren($vial)
    {
        $child = new FakeVial();
        $vial->addChild($child);
        $this->assertEquals(1,count($vial->getChildren()));
        $this->assertEquals($child,$vial->getChildren()->first());
        $vial->removeChild($child);
        $this->assertEquals(0,count($vial->getChildren()));
    }

    /**
     * @dataProvider vialProvider
     */
    public function testParent($vial)
    {
        $parent = new FakeVial();
        $vial->setParent($parent);
        $this->assertEquals($parent,$vial->getParent());
        $this->assertTrue($vial->isParentValid());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testMaleCrosses($vial)
    {
        $this->assertEquals(1,count($vial->getMaleCrosses()));
    }

    /**
     * @dataProvider vialProvider
     */
    public function testVirginCrosses($vial)
    {
        $this->assertEquals(1,count($vial->getVirginCrosses()));
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetCrosses($vial)
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection',$vial->getCrosses());
        $this->assertEquals(2,count($vial->getCrosses()));
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetLivingCrosses($vial)
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection',$vial->getLivingCrosses());
        $this->assertEquals(2,count($vial->getLivingCrosses()));
        $vial->getLivingCrosses()->first()->setTrashed(true);
        $this->assertEquals(1,count($vial->getLivingCrosses()));
    }

    /**
     * @dataProvider vialProvider
     */
    public function testLabelPrinted($vial)
    {
        $vial->setLabelPrinted(true);
        $this->assertTrue($vial->isLabelPrinted());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testTrashed($vial)
    {
        $vial->setTrashed(true);
        $this->assertTrue($vial->isTrashed());
    }

    public function testPosition()
    {
        $vial = new FakeVial();
        $vial->setSetupDate(new \DateTime());
        $this->assertNull($vial->getPosition());
        $position = new RackPosition(new Rack(1,1),1,1);
        $vial->setPosition($position);
        $this->assertEquals($position,$vial->getPosition());
        $this->assertEquals($vial,$position->getContents());
        
        $newPosition = $this->getPosition();
        $vial->setPosition($newPosition);
        $this->assertEquals($position,$vial->getPrevPosition());
        $this->assertEquals($newPosition,$vial->getPosition());
        
        return $vial;
    }

    /**
     * @dataProvider vialProvider
     */
    public function testPrevPosition($vial)
    {
        $this->assertNull($vial->getPrevPosition());
        $position = new RackPosition(new Rack(1,1),1,1);
        $vial->setPrevPosition($position);
        $this->assertEquals($position,$vial->getPrevPosition());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testIncubator($vial)
    {
        $this->assertNull($vial->getIncubator());
        $incubator = new Incubator();
        $vial->setIncubator($incubator);
        $this->assertEquals($incubator,$vial->getIncubator());
    }
    
    /**
     * @depends testPosition
     */
    public function testRackIncubator($vial)
    {
        $this->assertInstanceOf('VIB\FliesBundle\Entity\Incubator',$vial->getIncubator());
        $this->assertEquals($vial->getPosition()->getRack()->getIncubator(),$vial->getIncubator());
    }

    /**
     * @depends testPosition
     */
    public function testGetLocation($vial)
    {
        $this->assertEquals('Test R000000 A01',$vial->getLocation());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetTemperature($vial)
    {
        $this->assertEquals(21,$vial->getTemperature());
    }
    
    /**
     * @depends testPosition
     */
    public function testGetTemperatureIncubator($vial)
    {
        $this->assertEquals(28,$vial->getTemperature());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetGenerationTime($vial)
    {
        $this->assertEquals(13,$vial->getGenerationTime());
    }
    
    /**
     * @depends testPosition
     */
    public function testGetGenerationTimeIncubator($vial)
    {
        $this->assertEquals(7,$vial->getGenerationTime());
    }
    
    /**
     * @dataProvider vialProvider
     */
    public function testGetProgress($vial)
    {
        $date = new \DateTime();
        $vial->setSetupDate($date);
        $this->assertEquals(0,$vial->getProgress());
        $date->sub(new \DateInterval('P13D'));
        $vial->setSetupDate($date);
        $this->assertEquals(1,$vial->getProgress());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetDefaultFlipDate($vial)
    {
        $date = clone $this->date;
        $date->add(new \DateInterval('P26D'));
        $this->assertEquals($date,$vial->getDefaultFlipDate());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetRealFlipDate($vial)
    {
        $this->assertEquals($vial->getDefaultFlipDate(),$vial->getRealFlipDate());
        $vial->setFlipDate($date = $this->date);
        $this->assertEquals($date, $vial->getRealFlipDate());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testIsAlive($vial)
    {
        $date = new \DateTime();
        $vial->setSetupDate($date);
        $this->assertTrue($vial->isAlive());
        $vial->setTrashed(true);
        $this->assertFalse($vial->isAlive());
        $vial->setTrashed(false);
        $date = clone $this->date;
        $date->sub(new \DateInterval('P2M'));
        $vial->setSetupDate($date);
        $this->assertFalse($vial->isAlive());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetType($vial)
    {
        $this->assertEquals('', $vial->getType());
    }
    
    public function vialProvider()
    {
        $vial = new FakeVial();
        return array(array($vial));
    }
    
    protected function setUp()
    {
        $this->date = new \DateTime('2000-01-01 00:00:00');
    }
    
    protected function getPosition()
    {
        $incubator = new Incubator();
        $incubator->setName('Test');
        $incubator->setTemperature(28);
        $rack = new Rack(1,1);
        $rack->setIncubator($incubator);
        return new RackPosition($rack,1,1);
    }
}

class FakeVial extends Vial
{
    public function __construct(Vial $template = null, $flip = true)
    {
        parent::__construct($template, $flip);
        $this->id = 1;
        $this->setupDate = new \DateTime('2000-01-01 00:00:00');
        $maleCross = new CrossVial();
        $virginCross = new CrossVial();
        $this->maleCrosses->add($maleCross);
        $this->virginCrosses->add($virginCross);
    }
}
