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
use VIB\FliesBundle\Entity\StockVial;
use VIB\FliesBundle\Entity\Stock;


class StockVialTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @dataProvider vialProvider
     */
    public function testGetLabelText($vial)
    {
        $this->assertEquals('Test',$vial->getLabelText());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetAltLabelText($vial)
    {
        $this->assertEquals('test',$vial->getAltLabelText());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testAddChild($vial)
    {
        $child = new StockVial();
        $vial->addChild($child);
        $this->assertEquals($vial->getStock(),$vial->getChildren()->first()->getStock());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testSetParent($vial)
    {
        $parent = new StockVial();
        $vial->setParent($parent);
        $this->assertEquals($parent->getStock(),$vial->getStock());
    }

    /**
     * @dataProvider vialParentProvider
     */
    public function testIsParentValid($vial, $parent, $result)
    {
        $vial->setParent($parent);
        $this->assertEquals($result, $vial->isParentValid());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testGetType($vial)
    {
        $this->assertEquals('stock', $vial->getType());
    }

    /**
     * @dataProvider vialProvider
     */
    public function testStock($vial)
    {
        $this->assertNotNull($vial->getStock());
        $vial->setStock();
        $this->assertNull($vial->getStock());
        $stock = new Stock();
        $vial->setStock($stock);
        $this->assertEquals($stock, $vial->getStock());
    }
    
    public function vialProvider()
    {
        $vial = new FakeStockVial();
        return array(array($vial));
    }
    
    public function vialParentProvider()
    {
        return array(
            array(
                new FakeStockVial(),
                new Vial(),
                false
            ),
            array(
                new FakeStockVial(),
                new StockVial(),
                true
            )
        );
    }
}

class FakeStockVial extends StockVial
{
    public function __construct(Vial $template = null, $flip = true)
    {
        parent::__construct($template, $flip);
        $this->id = 1;
        $this->stock = new Stock();
        $this->stock->setName('Test');
        $this->stock->setGenotype('test');
    }
}