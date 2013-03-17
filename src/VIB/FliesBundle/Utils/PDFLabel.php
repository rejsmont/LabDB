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

namespace VIB\FliesBundle\Utils;

use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\TCPDFBundle\Controller\TCPDFController;
use PHP_IPP\IPP\CupsPrintIPP;

/**
 * Handle PDF Label generation
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class PDFLabel {
    
    /**
     * @var Tecnick\TCPDF\TCPDF $pdf
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
     * @param WhiteOctober\TCPDFBundle\Controller\TCPDFController $TCPDF
     * @param string $printHost
     * @param string $printQueue
     */ 
    public function __construct(TCPDFController $TCPDF,$printHost,$printQueue) {
        $this->pdf = $this->prepareLabelPDF($TCPDF);
        $this->printHost = $printHost;
        $this->printQueue = $printQueue;
    }
    
    /**
     * Add vial label to PDF
     * 
     * @param integer $barcode
     * @param datetime $date
     * @param string $text
     */    
    public function addFlyLabel($barcode,$date,$text,$owner = '') {
        $this->pdf->AddPage();
        $this->pdf->write2DBarcode(
                sprintf("%06d",$barcode),
                'QRCODE,H',
                2,2,15,15,
                $this->get2DBarcodeStyle());
        $this->pdf->setCellPaddings(0, 0, 0, 0);
        $this->pdf->setCellMargins(0, 0, 0, 0);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->MultiCell(30, 12.5, $text,0,'C',0,1,20,2,true,0,false,true,16.5,'T',true);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->MultiCell(30,6,$date->format("d.m.Y"),0,'C',0,1,20,18,true,0,false,true,6,'B',true);
        $this->pdf->MultiCell(15,6,sprintf("%06d",$barcode),0,'C',0,1,2,15,true,0,false,true,6,'B',true);
        $this->pdf->MultiCell(25,6,sprintf($owner),0,'L',0,1,2,18,true,0,false,true,6,'B',true);
    }
    
    /**
     * Add vial label to PDF
     * 
     * @param integer $barcode
     * @param string $text
     */    
    public function addRackLabel($barcode,$text) {
        $this->pdf->AddPage();
        $this->pdf->write2DBarcode(
                sprintf("R%06d",$barcode),
                'QRCODE,H',
                2,2,15,15,
                $this->get2DBarcodeStyle());
        $this->pdf->setCellPaddings(0, 0, 0, 0);
        $this->pdf->setCellMargins(0, 0, 0, 0);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->MultiCell(30, 12.5, $text,0,'C',0,1,20,2,true,0,false,true,16.5,'T',true);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->MultiCell(15,6,sprintf("R%06d",$barcode),0,'C',0,1,2,17.5,true,0,false,true,6,'B',true);
    }
    
    public function output() {
        return new Response($this->pdf->Output('', 'S'),200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="labels.pdf"'));
    }
    
    public function printPDF() {
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
     * @return Tecnick\TCPDF\TCPDF
     */ 
    private function prepareLabelPDF(TCPDFController $TCPDF) {
        
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
     * Generate style for 1D barcode
     * 
     * @return array
     */ 
    private function get1DBarcodeStyle() {
        
        $style = array(
            'position' => '',
            'align' => '',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => false,
            'border' => false,
            'hpadding' => '0',
            'vpadding' => '0',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false);
        
        return $style;
    }
    
    /**
     * Generate style for 2D barcode
     * 
     * @return array
     */     
    private function get2DBarcodeStyle() {
        
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

?>
