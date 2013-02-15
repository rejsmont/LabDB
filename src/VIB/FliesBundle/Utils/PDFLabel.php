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
     * Construct PDFLabel
     * 
     * @param WhiteOctober\TCPDFBundle\Controller\TCPDFController $TCPDF
     */ 
    public function __construct(TCPDFController $TCPDF)
    {
        $this->pdf = $this->prepareLabelPDF($TCPDF);
    }
    
    /**
     * Add vial label to PDF
     * 
     * @param integer $barcode
     * @param datetime $date
     * @param string $text
     */    
    public function addFlyLabel($barcode,$date,$text) {
        $this->pdf->AddPage();
        $this->pdf->write2DBarcode(
                sprintf("%06d",$barcode),
                'QRCODE,H',
                6,2,12.5,12.5,
                $this->get2DBarcodeStyle());
        $this->pdf->StartTransform();
        $this->pdf->Rotate(270,39.8,19.1);
        $this->pdf->write1DBarcode(
                sprintf("%06d",$barcode),
                'C128C',
                22.7,13.1,15.1,4,'',
                $this->get1DBarcodeStyle(),'N');
        $this->pdf->StopTransform();
        $this->pdf->setCellPaddings(0, 0, 0, 0);
        $this->pdf->setCellMargins(0, 0, 0, 0);
        $this->pdf->SetFont('helvetica', 'B', 8);
        $this->pdf->MultiCell(20.5, 12.5, $text,0,'C',0,1,19.5,2,true,0,false,true,11.5,'T',true);
        $this->pdf->SetFont('helvetica', '', 6);
        $this->pdf->MultiCell(20.5,4,$date->format("d.m.Y"),0,'C',0,1,19.5,13.7,true,0,false,true,4,'B',true);
        $this->pdf->MultiCell(12.5,4,sprintf("%06d",$barcode),0,'C',0,1,6,13.7,true,0,false,true,4,'B',true);
    }
    
    public function output() {
        return new Response($this->pdf->Output('', 'S'),200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="labels.pdf"'));
    }
    
    /**
     * Generate label PDF
     * 
     * @return Tecnick\TCPDF\TCPDF
     */ 
    private function prepareLabelPDF(TCPDFController $TCPDF) {
        
        $pdf = $TCPDF->create('L', 'mm', array(50.8,19.1), true, 'UTF-8', false);

        $pdf->SetCreator(false);
        $pdf->SetAuthor(false);
        $pdf->SetTitle(false);
        $pdf->SetSubject(false);
        $pdf->SetKeywords(false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(6,2,5);
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
