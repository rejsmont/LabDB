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

use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Entity\CrossVial;
use VIB\FliesBundle\Entity\StockVial;
use VIB\FliesBundle\Entity\Stock;

class CrossVialTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider vialProvider
     */
    public function testConstruct($vial)
    {
        $vial->setSetupDate(new \DateTime());
        $this->assertNull($vial->isSuccessful());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $vial->getStocks());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSetTrashed($vial)
    {
        $vial->setSetupDate(new \DateTime());
        $vial->setTrashed(true);
        $this->assertFalse($vial->isSuccessful());
        $vial->setTrashed(false);
        $this->assertNull($vial->isSuccessful());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSterile($vial)
    {
        $this->assertFalse($vial->isSterile());
        $vial->setSterile(true);
        $this->assertTrue($vial->isSterile());
        $stock = new Stock();
        $vial->getStocks()->add($stock);
        $this->assertFalse($vial->isSterile());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testHasProduced($vial)
    {
        $this->assertFalse($vial->hasProduced());
        $stock = new Stock();
        $vial->getStocks()->add($stock);
        $this->assertTrue($vial->hasProduced());
        $vial->getStocks()->clear();
        $cross = new CrossVial();
        $vial->getMaleCrosses()->add($cross);
        $this->assertTrue($vial->hasProduced());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSuccessful($vial)
    {
        $vial->setSetupDate(new \DateTime());
        $this->assertNull($vial->isSuccessful());
        $stock = new Stock();
        $vial->getStocks()->add($stock);
        $this->assertTrue($vial->isSuccessful());
        $vial->getStocks()->clear();
        $vial->setSuccessful(true);
        $this->assertTrue($vial->isSuccessful());
        $vial->setSuccessful(false);
        $this->assertFalse($vial->isSuccessful());
        $vial->setSuccessful(null);
        $vial->setTrashed(true);
        $this->assertFalse($vial->isSuccessful());
    }

    /**
     * @dataProvider vialOutcomeProvider
     */
    public function testOutcome($vial, $outcome)
    {
        $vial->setSetupDate(new \DateTime());
        $this->assertEquals('undefined',$vial->getOutcome());
        $vial->setOutcome($outcome);
        $this->assertEquals($outcome,$vial->getOutcome());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetLabelText($vial)
    {
        $this->assertEquals("test virgin ☿ ✕ test male ♂", $vial->getLabelText());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testAddChild($vial)
    {
        $child = new CrossVial();
        $vial->addChild($child);
        $this->assertEquals($vial->getVirgin(), $child->getVirgin());
        $this->assertEquals($vial->getVirginName(), $child->getVirginName());
        $this->assertEquals($vial->getMale(), $child->getMale());
        $this->assertEquals($vial->getMaleName(), $child->getMaleName());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testParent($vial)
    {
        $parent = new CrossVial();
        $vial->setParent($parent);
        $this->assertEquals($parent->getVirgin(), $vial->getVirgin());
        $this->assertEquals($parent->getVirginName(), $vial->getVirginName());
        $this->assertEquals($parent->getMale(), $vial->getMale());
        $this->assertEquals($parent->getMaleName(), $vial->getMaleName());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetType($vial)
    {
        $this->assertEquals("cross", $vial->getType());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetName($vial)
    {
        $this->assertEquals("test virgin ☿ ✕ test male ♂", $vial->getLabelText());
    }

    /**
     * @dataProvider crossParentProvider
     */
    public function testMale($vial, $male, $maleName)
    {
        $this->assertNotNull($vial->getMale());
        $vial->setMale();
        $this->assertNull($vial->getMale());
        $vial->setMale($male);
        $this->assertEquals($male, $vial->getMale());
        $this->assertEquals($maleName, $vial->getMaleName());
        if ($male instanceof CrossVial) {
            $this->assertFalse($vial->isMaleValid());
            $vial->setMaleName('test');
        }
        $this->assertTrue($vial->isMaleValid());
    }

    /**
     * @dataProvider crossParentNameProvider
     */
    public function testMaleName($vial, $male, $maleName)
    {
        $this->assertNotEmpty($vial->getMaleName());
        $vial->setMaleName('');
        $this->assertEmpty($vial->getMaleName());
        $vial->setMale($male);
        if ($male instanceof StockVial) {
            $this->assertEquals($male->getStock()->getGenotype(), $vial->getMaleName());
        }
        $vial->setMaleName($maleName);
        $this->assertEquals($maleName, $vial->getMaleName());
    }

    /**
     * @dataProvider crossParentProvider
     */
    public function testVirgin($vial, $virgin, $virginName)
    {
        $this->assertNotNull($vial->getVirgin());
        $vial->setVirgin();
        $this->assertNull($vial->getVirgin());
        $vial->setVirgin($virgin);
        $this->assertEquals($virgin, $vial->getVirgin());
        $this->assertEquals($virginName, $vial->getVirginName());
        if ($virgin instanceof CrossVial) {
            $this->assertFalse($vial->isVirginValid());
            $vial->setVirginName('test');
        }
        $this->assertTrue($vial->isVirginValid());
    }

    /**
     * @dataProvider crossParentNameProvider
     */
    public function testVirginName($vial, $virgin, $virginName)
    {
        $this->assertNotEmpty($vial->getVirginName());
        $vial->setVirginName('');
        $this->assertEmpty($vial->getVirginName());
        $vial->setVirgin($virgin);
        if ($virgin instanceof StockVial) {
            $this->assertEquals($virgin->getStock()->getGenotype(), $vial->getVirginName());
        }
        $vial->setVirginName($virginName);
        $this->assertEquals($virginName, $vial->getVirginName());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetStocks($vial)
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $vial->getStocks());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetProgress($vial)
    {
        $date = new \DateTime();
        $vial->setSetupDate($date);
        $this->assertEquals(0,$vial->getProgress());
        $date->sub(new \DateInterval('P15D'));
        $vial->setSetupDate($date);
        $this->assertEquals(1,$vial->getProgress());
        $vial->setParent(new CrossVial());
        $date->add(new \DateInterval('P2D'));
        $vial->setSetupDate($date);
        $this->assertEquals(1,$vial->getProgress());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetDefaultFlipDate($vial)
    {
        $date = clone $this->date;
        $date->add(new \DateInterval('P15D'));
        $this->assertEquals($date,$vial->getDefaultFlipDate());
        $date->sub(new \DateInterval('P2D'));
        $vial->setParent(new CrossVial());
        $this->assertEquals($date,$vial->getDefaultFlipDate());
    }

    public function vialProvider()
    {
        $vial = new FakeCrossVial();

        return array(array($vial));
    }

    public function vialOutcomeProvider()
    {
        return array(
            array(new FakeCrossVial(), 'successful'),
            array(new FakeCrossVial(), 'failed'),
            array(new FakeCrossVial(), 'sterile'),
            array(new FakeCrossVial(), 'undefined')
        );
    }

    public function crossParentProvider()
    {
        $stock = new Stock();
        $stock->setGenotype('test stock');
        $stockVial = new StockVial();
        $stockVial->setStock($stock);

        return array(
            array(new FakeCrossVial(), null, ''),
            array(new FakeCrossVial(), new Vial(), ''),
            array(new FakeCrossVial(), $stockVial, 'test stock'),
            array(new FakeCrossVial(), new CrossVial(), '')
        );
    }

    public function crossParentNameProvider()
    {
        $stock = new Stock();
        $stock->setGenotype('test stock');
        $stockVial = new StockVial();
        $stockVial->setStock($stock);

        return array(
            array(new FakeCrossVial(), null, 'test'),
            array(new FakeCrossVial(), $stockVial, 'test male'),
            array(new FakeCrossVial(), new CrossVial(), 'test')
        );
    }

    protected function setUp()
    {
        $this->date = new \DateTime('2000-01-01 00:00:00');
    }
}

class FakeCrossVial extends CrossVial
{
    public function __construct(Vial $template = null, $flip = true)
    {
        parent::__construct($template, $flip);
        $this->id = 1;
        $this->setupDate = new \DateTime('2000-01-01 00:00:00');
        $this->male = new StockVial();
        $this->male->setStock(new Stock());
        $this->maleName = 'test male';
        $this->virgin = new StockVial();
        $this->virgin->setStock(new Stock());
        $this->virginName = 'test virgin';
    }
}
