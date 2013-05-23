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

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;
use VIB\FliesBundle\Entity\CrossVial;

class StockTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider stockProvider
     */
    public function testConstruct($stock)
    {
        $this->assertFalse($stock->isVerified());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $stock->getVials());
        $this->assertEquals(1,count($stock->getVials()));
    }

    /**
     * @dataProvider stockProvider
     */
    public function testToString($stock)
    {
        $this->assertEquals($stock->getName(), (string) $stock);
    }

    /**
     * @dataProvider stockProvider
     */
    public function testName($stock)
    {
        $this->assertEmpty($stock->getName());
        $stock->setName('test');
        $this->assertEquals('test', $stock->getName());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testGetLabel($stock)
    {
        $this->assertEquals($stock->getName(), $stock->getLabel());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testGenotype($stock)
    {
        $this->assertEmpty($stock->getGenotype());
        $stock->setGenotype('test/test');
        $this->assertEquals('test / test', $stock->getGenotype());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testNotes($stock)
    {
        $this->assertEmpty($stock->getNotes());
        $stock->setNotes('test');
        $this->assertEquals('test', $stock->getNotes());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testVendor($stock)
    {
        $this->assertEmpty($stock->getVendor());
        $stock->setVendor('test');
        $this->assertEquals('test', $stock->getVendor());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testInfoURL($stock)
    {
        $this->assertEmpty($stock->getInfoURL());
        $stock->setInfoURL('test');
        $this->assertEquals('test', $stock->getInfoURL());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testVerified($stock)
    {
        $this->assertFalse($stock->isVerified());
        $stock->setVerified(true);
        $this->assertTrue($stock->isVerified());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testVials($stock)
    {
        $vial = new StockVial();
        $stock->addVial($vial);
        $this->assertContains($vial, $stock->getVials());
        $stock->removeVial($vial);
        $this->assertNotContains($vial, $stock->getVials());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testGetLivingVials($stock)
    {
        $vial = new StockVial();
        $stock->addVial($vial);
        $this->assertContains($vial, $stock->getLivingVials());
        $vial->getSetupDate()->sub(new \DateInterval('P6M'));
        $this->assertNotContains($vial, $stock->getLivingVials());
    }

    /**
     * @dataProvider stockProvider
     */
    public function testSourceCross($stock)
    {
        $this->assertNull($stock->getSourceCross());
        $cross = new CrossVial();
        $stock->setSourceCross($cross);
        $this->assertEquals($cross, $stock->getSourceCross());
    }

    public function stockProvider()
    {
        $stock = new Stock();

        return array(array($stock));
    }
}
