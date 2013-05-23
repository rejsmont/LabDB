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

namespace VIB\FliesBundle\Tests\Label;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Label\PDFLabel;
use VIB\FliesBundle\Entity\Vial;

class PDFLabelTest extends \PHPUnit_Framework_TestCase
{
    private $PDF;
    private $om;
    private $TCPDFController;
    private $TCPDF;

    /**
     * @dataProvider vialProvider
     */
    public function testAddLabel($vials)
    {
        $this->TCPDF->expects($this->exactly(2))->method('MultiCell');
        $this->om->expects($this->once())->method('getOwner');
        $this->PDF->addLabel($vials);
    }

    public function testOutputPDF()
    {
        $response = $this->PDF->outputPDF();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response',$response);
        $this->assertEquals(200,$response->getStatusCode());
    }

    protected function setUp()
    {
        $this->om = $this->getMockBuilder('VIB\CoreBundle\Doctrine\ObjectManager')
            ->disableOriginalConstructor()->getMock();
        $this->TCPDFController = $this->getMockBuilder('WhiteOctober\TCPDFBundle\Controller\TCPDFController')
            ->disableOriginalConstructor()->getMock();
        $this->TCPDF = $this->getMockBuilder('\TCPDF')
            ->disableOriginalConstructor()->getMock();
        $this->TCPDFController->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->TCPDF));
        $this->PDF = new PDFLabel($this->om, $this->TCPDFController, null, null);
    }

    public function vialProvider()
    {
        $vial = new Vial();
        $collection = new ArrayCollection();
        $collection->add($vial);

        return array(
          array($vial),
          array($collection),
        );
    }
}
