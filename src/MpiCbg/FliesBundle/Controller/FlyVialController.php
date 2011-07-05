<?php

namespace MpiCbg\FliesBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\Collections\ArrayCollection;

use MpiCbg\FliesBundle\Entity\FlyVial;
use MpiCbg\FliesBundle\Wrapper\Barcode\FlyVial as FlyVialBarcode;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelector;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelectorItem;
use MpiCbg\FliesBundle\Form\FlyVialBarcodeType;
use MpiCbg\FliesBundle\Form\CollectionSelectorType;

use Tecnick\TCPDF\TCPDF;

class FlyVialController extends Controller
{
    /**
     * List vials
     * 
     * @Route("/vials/list/{filter}", name="flyvial_list")
     * @Template()
     */
    public function listAction($filter = 'living')
    {
        $em = $this->get('doctrine.orm.entity_manager');
        
        switch($filter) {
            case 'stock':
                $vials = $em->getRepository('MpiCbgFliesBundle:FlyVial')->findAllLivingStocks();
                $header = 'Stock vials';
                break;
            case 'cross':
                $vials = $em->getRepository('MpiCbgFliesBundle:FlyVial')->findAllLivingCrosses();
                $header = 'Cross vials';
                break;
            case 'all':
                $vials = $em->getRepository('MpiCbgFliesBundle:FlyVial')->findAll();
                $header = 'Vials (including trashed)';
                break;
            case 'living':
            default:
                $vials = $em->getRepository('MpiCbgFliesBundle:FlyVial')->findAllLiving();
                $header = 'Vials';
        }
        
        $vialsSelector = new CollectionSelector($vials);

        foreach ($vialsSelector->getItems() as $vialsSelectorItem) {
            
            $vial = $vialsSelectorItem->getItem();
                        
            if (isset($vial)) {
                
                if ((is_a($vial,"MpiCbg\FliesBundle\Entity\FlyVial"))||
                    (is_subclass_of($vial,"MpiCbg\FliesBundle\Entity\FlyVial"))) {
                    $vialsSelectorItem->setSelected(! $vial->isLabelPrinted());
                }
            }
            
        }
        
        $form = $this->get('form.factory')
                     ->create(new CollectionSelectorType(), $vialsSelector);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {    
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                return $this->handleBatchAction($vialsSelector, $filter);
            }
        }
        
        return array('vials' => $vialsSelector,
                     'form' => $form->createView(),
                     'header' => $header);
    }
    
    public function handleBatchAction($vialsSelector, $filter) {

        switch($vialsSelector->getAction()) {
            case 'label':
                return $this->generateLabels($vialsSelector, $filter);
                break;
            case 'flip':
                return $this->flipBottles($vialsSelector, $filter);
                break;
            case 'trash':
                return $this->trashBottles($vialsSelector, $filter);
                break;
            default:
                return $this->redirect($this->generateUrl('flyvial_list', array('filter' => $filter)));
                break;
        }
    }
    
    public function generateLabels($vialsSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        $pdf = $this->prepareLabelPDF();
        
        foreach ($vialsSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $vial = $item->getItem();
                $vial->setLabelPrinted(true);
                $em->persist($vial);
                $pdf = $this->addFlyLabel($pdf, $vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
            }
        }
        
        $em->flush();

        return new Response(
            $pdf->Output('', 'S'),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="labels.pdf',
            )
        );
    }
    
    public function flipBottles($vialsSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');

        foreach ($vialsSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $vial = $item->getItem();
                $newvial = new FlyVial($vial);
                $em->persist($newvial);
            }
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('flyvial_list', array('filter' => $filter)));
    }
    
    public function trashBottles($vialsSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        
        foreach ($vialsSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $vial = $item->getItem();
                $vial->setTrashed(true);
                $em->persist($vial);
            }
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('flyvial_list', array('filter' => $filter)));
    }
    
    /**
     * Show vial
     * 
     * @Route("/vials/show/{id}", name="flyvial_show")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyVial")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('MpiCbgFliesBundle:FlyVial', $id);
        
        return array('vial' => $vial);
    }
    
    
    /**
     * Create new vial
     * 
     * @Route("/vials/new", name="flyvial_create")
     * @Template()
     */
    public function createAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = new FlyVial();
        $vialBarcode = new FlyVialBarcode($em, $vial);
        
        $form = $this->get('form.factory')
                ->create(new FlyVialBarcodeType(), $vialBarcode);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($vial);
                $em->flush();
                return $this->redirect($this->generateUrl('flyvial_show',array('id' => $vial->getId())));
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit vial
     * 
     * @Route("/vials/edit/{id}", name="flyvial_edit")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyVial")
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('MpiCbgFliesBundle:FlyVial', $id);
        $vialBarcodes = new FlyVialBarcode($em, $vial);
        
        $form = $this->get('form.factory')
                ->create(new FlyVialBarcodeType(), $vialBarcodes);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($vial);
                $em->flush();
                return $this->redirect($this->generateUrl('flyvial_show',array('id' => $vial->getId())));
            }
        }
        
        return array(
            'vial' => $vial,
            'form' => $form->createView());
    }

    /**
     * Delete vial
     * 
     * @Route("/vials/delete/{id}", name="flyvial_delete")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyVial")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('MpiCbgFliesBundle:FlyVial', $id);

        $em->remove($vial);
        $em->flush();
        return $this->redirect($this->generateUrl('flyvial_list'));
    }
    
    private function addFlyLabel(TCPDF $pdf,$barcode,$date,$text) {
        $pdf->AddPage();
        $pdf->write2DBarcode(sprintf("%06d",$barcode), 'QRCODE,H', 6, 2, 12.5, 12.5, $this->get2DBarcodeStyle());
        $pdf->StartTransform();
        $pdf->Rotate(270,39.8,19.1);
        $pdf->write1DBarcode(sprintf("%06d",$barcode), 'C128C',
                             22.7, 13.1, 15.1, 4, '',
                             $this->get1DBarcodeStyle(), 'N');
        $pdf->StopTransform();
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->setCellMargins(0, 0, 0, 0);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->MultiCell(20.5, 12.5, $text,0,'C',0,1,19.5,2,true,0,false,true,11.5,'T',true);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->MultiCell(20.5,4,$date->format("d.m.Y"),0,'C',0,1,19.5,13.7,true,0,false,true,4,'B',true);
        $pdf->MultiCell(12.5,4,sprintf("%06d",$barcode),0,'C',0,1,6,13.7,true,0,false,true,4,'B',true);
        
        return $pdf;
    }
    
    private function prepareLabelPDF() {
        
        $pdf = new TCPDF('L', 'mm', array(50.8,19.1), true, 'UTF-8', false);

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
            'text' => false,
        );
        
        return $style;
    }
    
    private function get2DBarcodeStyle() {
        
        $style = array(
            'border' => 0,
            'vpadding' => '0',
            'hpadding' => '0',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        
        return $style;
    }
}
