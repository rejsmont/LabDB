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

namespace VIB\FliesBundle\Label;

use VIB\CoreBundle\Doctrine\ObjectManager;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\TCPDFBundle\Controller\TCPDFController;
use PHP_IPP\IPP\CupsPrintIPP;

/**
 * Handle PDF Label generation
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PDFLabel
{

    /**
     * @var \VIB\CoreBundle\Doctrine\ObjectManager $om
     */
    private $om;

    /**
     * @var \Tecnick\TCPDF\TCPDF $pdf
     */
    private $pdf;

    /**
     * @var string $printHost
     */
    private $printHost;

    /**
     * @var string $printQueue
     */
    private $printQueue;

    /**
     * Construct PDFLabel
     *
     * @param \VIB\CoreBundle\Doctrine\ObjectManager               $om
     * @param \WhiteOctober\TCPDFBundle\Controller\TCPDFController $TCPDF
     * @param string                                               $printHost
     * @param string                                               $printQueue
     */
    public function __construct(ObjectManager $om, TCPDFController $TCPDF, $printHost, $printQueue)
    {
        $this->om = $om;
        $this->pdf = $this->prepareLabelPDF($TCPDF);
        $this->printHost = $printHost;
        $this->printQueue = $printQueue;
    }

    /**
     * Add label(s) to PDF
     *
     * @param mixed $entities
     */
    public function addLabel($entities, $alternative = false)
    {
        if (($entity = $entities) instanceof LabelInterface) {
            $barcode = $entity->getLabelBarcode();
            if (($entity instanceof AltLabelInterface)&&($alternative)) {
                $text = $entity->getAltLabelText();
            } else {
                $text = $entity->getLabelText();
            }
            $owner = $this->om->getOwner($entity);
            $date = ($entity instanceof LabelDateInterface) ? $entity->getLabelDate()->format("d.m.Y") : null;
            $this->pdf->AddPage();
            $this->pdf->SetAutoPageBreak(false);
            $this->pdf->write2DBarcode($barcode,'DATAMATRIX',2,2,15,15,$this->get2DBarcodeStyle());
            $this->pdf->setCellPaddings(0, 0, 0, 0);
            $this->pdf->setCellMargins(0, 0, 0, 0);
            $this->pdf->SetFont('DejaVuSans', 'B', 12);
            $this->pdf->MultiCell(30, 12.5, $text,0,'C',0,1,20,2,true,0,false,true,18.5,'T',true);
            $this->pdf->SetFont('DejaVuSans', '', 7);
            $this->pdf->MultiCell(15,6,$barcode,0,'C',0,1,2,15,true,0,false,true,6,'B',true);
            if (null !== $date) {
                $this->pdf->MultiCell(30,6,$date,0,'C',0,1,20,18,true,0,false,true,6,'B',true);
            }
            if (null !== $owner) {
                $this->pdf->MultiCell(25,6,$owner,0,'L',0,1,2,18,true,0,false,true,6,'B',true);
            }
        } elseif ($entities instanceof Collection) {
            foreach ($entities as $entity) {
                $this->addLabel($entity, $alternative);
            }
        } elseif (null === $entities) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Label\LabelInterface or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Output generated PDF
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function outputPDF()
    {
        return new Response($this->pdf->Output('', 'S'),200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="labels.pdf"'));
    }

    /**
     * Print generated PDF
     *
     * @return string|boolean
     */
    public function printPDF()
    {
        $ipp = new CupsPrintIPP();
        $ipp->setLog('', 0, 0);
        $ipp->setHost($this->printHost);
        $ipp->setPrinterURI($this->printQueue);
        $ipp->setSides(1);
        $ipp->setData($this->pdf->Output('', 'S'));

        return $ipp->printJob();
    }

    /**
     * Generate label PDF
     *
     * @param  \WhiteOctober\TCPDFBundle\Controller\TCPDFController $TCPDF
     * @return Tecnick\TCPDF\TCPDF
     */
    private function prepareLabelPDF(TCPDFController $TCPDF)
    {
        $pdf = $TCPDF->create('L', 'mm', array(50.8,25.4), true, 'UTF-8', false);

        $pdf->SetCreator(false);
        $pdf->SetAuthor(false);
        $pdf->SetTitle(false);
        $pdf->SetSubject(false);
        $pdf->SetKeywords(false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(2,2);
        $pdf->SetAutoPageBreak(false);

        return $pdf;
    }

    /**
     * Generate style for 2D barcode
     *
     * @return array
     */
    private function get2DBarcodeStyle()
    {

        $style = array(
            'border' => 0,
            'vpadding' => '0',
            'hpadding' => '0',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1);

        return $style;
    }
}
