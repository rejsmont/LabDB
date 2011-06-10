<?php

namespace MpiCbg\FliesBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MpiCbg\FliesBundle\Entity\CultureBottle;
use MpiCbg\FliesBundle\Wrapper\Barcode\CultureBottle as CultureBottleBarcode;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelector;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelectorItem;
use MpiCbg\FliesBundle\Form\CultureBottleBarcodeType;
use MpiCbg\FliesBundle\Form\CollectionSelectorType;

use Tecnick\TCPDF\TCPDF;

class CultureBottleController extends Controller
{
    /**
     * List bottles
     * 
     * @Route("/bottles/{filter}", name="culturebottle_list")
     * @Template()
     */
    public function listAction($filter = 'living')
    {
        $em = $this->get('doctrine.orm.entity_manager');
        
        switch($filter) {
            case 'stock':
                $bottles = $em->getRepository('MpiCbgFliesBundle:CultureBottle')->findAllLivingStocks();
                $header = 'Stock bottles';
                break;
            case 'cross':
                $bottles = $em->getRepository('MpiCbgFliesBundle:CultureBottle')->findAllLivingCrosses();
                $header = 'Cross bottles';
                break;
            case 'all':
                $bottles = $em->getRepository('MpiCbgFliesBundle:CultureBottle')->findAll();
                $header = 'Bottles (including trashed)';
                break;
            case 'living':
            default:
                $bottles = $em->getRepository('MpiCbgFliesBundle:CultureBottle')->findAllLiving();
                $header = 'Bottles';
        }
        
        $bottlesSelector = new CollectionSelector($bottles);
        
        $form = $this->get('form.factory')
                     ->create(new CollectionSelectorType(), $bottlesSelector);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {    
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                return $this->handleBatchAction($bottlesSelector, $filter);
            }
        }
        
        return array('bottles' => $bottlesSelector,
                     'form' => $form->createView(),
                     'header' => $header);
    }
    
    public function handleBatchAction($bottlesSelector, $filter) {

        switch($bottlesSelector->getAction()) {
            case 'label':
                return $this->generateLabels($bottlesSelector, $filter);
                break;
            case 'flip':
                return $this->flipBottles($bottlesSelector, $filter);
                break;
            case 'trash':
                return $this->trashBottles($bottlesSelector, $filter);
                break;
            default:
                return $this->redirect($this->generateUrl('culturebottle_list', array('filter' => $filter)));
                break;
        }
    }
    
    public function generateLabels($bottlesSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        $pdf = $this->prepareLabelPDF();
        
        foreach ($bottlesSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $bottle = $item->getItem();
                $bottle->setHasLabel(true);
                $em->persist($bottle);
                $pdf = $this->addFlyLabel($pdf, $bottle->getId(), $bottle->getSetupDate(), $bottle->getLabelText());
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
    
    public function flipBottles($bottlesSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        
        foreach ($bottlesSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $bottle = $item->getItem();
                $newbottle = new CultureBottle($bottle);
                $em->persist($newbottle);
            }
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('culturebottle_list', array('filter' => $filter)));
    }
    
    public function trashBottles($bottlesSelector, $filter) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        
        foreach ($bottlesSelector->getItems() as $item) {
            
            if($item->isSelected()) {
                $bottle = $item->getItem();
                $bottle->setTrashed(true);
                $em->persist($bottle);
            }
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('culturebottle_list', array('filter' => $filter)));
    }
    
    /**
     * Show bottle
     * 
     * @Route("/bottles/show/{id}", name="culturebottle_show")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:CultureBottle")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bottle = $em->find('MpiCbgFliesBundle:CultureBottle', $id);
        
        return array('bottle' => $bottle);
    }
    
    
    /**
     * Create new bottle
     * 
     * @Route("/bottles/new", name="culturebottle_create")
     * @Template()
     */
    public function createAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bottle = new CultureBottle();
        $bottleBarcode = new CultureBottleBarcode($em, $bottle);
        
        $form = $this->get('form.factory')
                ->create(new CultureBottleBarcodeType(), $bottleBarcode);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($bottle);
                $em->flush();
                return $this->redirect($this->generateUrl('culturebottle_show',array('id' => $bottle->getId())));
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit bottle
     * 
     * @Route("/bottles/edit/{id}", name="culturebottle_edit")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:CultureBottle")
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bottle = $em->find('MpiCbgFliesBundle:CultureBottle', $id);
        $bottleBarcodes = new CultureBottleBarcode($em, $bottle);
        
        $form = $this->get('form.factory')
                ->create(new CultureBottleBarcodeType(), $bottleBarcodes);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($bottle);
                $em->flush();
                return $this->redirect($this->generateUrl('culturebottle_show',array('id' => $bottle->getId())));
            }
        }
        
        return array(
            'bottle' => $bottle,
            'form' => $form->createView());
    }

    /**
     * Delete bottle
     * 
     * @Route("/bottles/delete/{id}", name="culturebottle_delete")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:CultureBottle")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bottle = $em->find('MpiCbgFliesBundle:CultureBottle', $id);

        $em->remove($bottle);
        $em->flush();
        return $this->redirect($this->generateUrl('culturebottle_list'));
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
