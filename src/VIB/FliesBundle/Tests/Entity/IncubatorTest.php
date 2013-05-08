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

use VIB\FliesBundle\Entity\Incubator;


class IncubatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider incubatorProvider
     */
    public function testToString($incubator)
    {
        $this->assertEquals('New incubator', (string) $incubator);
    }
    
    /**
     * @dataProvider incubatorProvider
     */
    public function testName($incubator)
    {
        $this->assertEquals('New incubator', $incubator->getName());
        $incubator->setName('Test');
        $this->assertEquals('Test', $incubator->getName());
    }

    /**
     * @dataProvider incubatorProvider
     */
    public function testGetRacks($incubator)
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $incubator->getRacks());
    }
    
    /**
     * @dataProvider incubatorProvider
     */
    public function testGetVials($incubator)
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $incubator->getVials());
    }

    /**
     * @dataProvider incubatorProvider
     */
    public function testTemperature($incubator)
    {
        $this->assertEquals(25, $incubator->getTemperature());
        $incubator->setTemperature(28);
        $this->assertEquals(28, $incubator->getTemperature());
    }
    
    public function incubatorProvider()
    {
        $incubator = new Incubator();
        return array(array($incubator));
    }
}
