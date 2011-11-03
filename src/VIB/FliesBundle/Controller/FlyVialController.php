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

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use Doctrine\Common\Collections\ArrayCollection;

use Tecnick\TCPDF\TCPDF;

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Entity\ListCollection;
use VIB\FliesBundle\Form\FlyVialType;
use VIB\FliesBundle\Form\FlyVialSelectType;


/**
 * FlyVialController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyVialController extends Controller
{
    /**
     * List vials
     * 
     * @param string $filter
     * @return mixed
     * 
     * @Route("/vials", name="flyvial_list")
     * @Route("/vials/list/{filter}", name="flyvial_listfilter", defaults={"filter" = "living"})
     * @Template()
     */
    public function listAction($filter = 'living')
    {
        $em = $this->get('doctrine.orm.entity_manager');
        
        switch($filter) {
            case 'stock':
                $vials = $em->getRepository('VIBFliesBundle:FlyVial')->findAllLivingStocks();
                $header = 'Stock vials';
                break;
            case 'cross':
                $vials = $em->getRepository('VIBFliesBundle:FlyVial')->findAllLivingCrosses();
                $header = 'Cross vials';
                break;
            case 'all':
                $vials = $em->getRepository('VIBFliesBundle:FlyVial')->findAll();
                $header = 'Vials (including trashed)';
                break;
            case 'living':
            default:
                $vials = $em->getRepository('VIBFliesBundle:FlyVial')->findAllLiving();
                $header = 'Vials';
        }
        
        return array(
            'vials' => $vials,
            'header' => $header);
    }
    
    /**
     * Handle batch action
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return mixed
     */
    public function handleBatchAction($vials) {

        switch($vialsSelector->getAction()) {
            case 'label':
                return $this->generateLabels($vials);
                break;
            case 'flip':
                return $this->flipVials($vials);
                break;
            case 'trash':
                return $this->trashVials($vials);
                break;
            default:
                return $this->redirect($this->generateUrl('flyvial_list'));
                break;
        }
    }
    
    /**
     * Generate vial labels
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return mixed
     */    
    public function generateLabels($vials) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        $pdf = $this->prepareLabelPDF();
        
        foreach ($vials as $vial) {
            $vial = $item->getItem();
            $vial->setLabelPrinted(true);
            $em->persist($vial);
            $pdf = $this->addFlyLabel($pdf, $vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
        }
        
        $em->flush();

        return new Response($pdf->Output('', 'S'),200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="labels.pdf'));
    }
    
    /**
     * Flip vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return mixed
     */     
    public function flipVials($vials) {
        
        $em = $this->get('doctrine.orm.entity_manager');

        $newvials = new ArrayCollection();
        
        foreach ($vials as $vial) {       
            $newvial = new FlyVial($vial);
            $newvials->add($newvial);
            $em->persist($newvial);
        }
        
        $em->flush();

        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        
        $aclProvider = $this->get('security.acl.provider');
        
        foreach ($newvials as $vial) {
            $objectIdentity = ObjectIdentity::fromDomainObject($vial);
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);
        }
        
        return $this->redirect($this->generateUrl('flyvial_list'));
    }
    
    /**
     * Trash vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return mixed
     */  
    public function trashVials($vials) {
        
        $em = $this->get('doctrine.orm.entity_manager');
        
        foreach ($vials as $vial) {
            $vial = $item->getItem();
            $vial->setTrashed(true);
            $em->persist($vial);
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('flyvial_list'));
    }

    /**
     * Select vials
     * 
     * @return mixed
     * 
     * @Route("/vials/select", name="flyvial_select")
     * @Template()
     */
    public function selectAction() {
        
        $list = new ListCollection();
        $form = $this->createForm(new FlyVialSelectType(), $list);

        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {

            }
        }
        
        return array(
            'header' => 'Select vials',
            'list' => $list,
            'form' => $form->createView()
        );
    }
    
    /**
     * Show vial
     * 
     * @param integer $id
     * @return mixed
     * 
     * @Route("/vials/show/{id}", name="flyvial_show")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */
    public function showAction($id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        
        return array('vial' => $vial);
    }
    
    /**
     * Return vial as JSON
     * 
     * @param integer $id
     * @return mixed
     * 
     * @Route("/vials/getJSON/{id}", name="flyvial_showJSON")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */    
    public function getJSONAction($id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        $stock = $vial->setStock($vial->getStock());
        
        $serializer = $this->get('serializer');
        
        return new Response($serializer->serialize($vial, 'json'));
    }
    
    /**
     * Return vial as form row
     * 
     * @param integer $id
     * @return mixed
     * 
     * @Route("/vials/getFormRow/{id}", name="flyvial_getFormRow")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */    
    public function getFormRowAction($id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        
        return array('vial' => $vial);;
    }
    
    
    /**
     * Create new vial
     * 
     * @return mixed
     * 
     * @Route("/vials/new", name="flyvial_create")
     * @Template()
     */
    public function createAction() {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = new FlyVial();
        
        $form = $this->get('form.factory')->create(new FlyVialType(), $vial);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($vial);
                $em->flush();
                
                $securityContext = $this->get('security.context');
                $user = $securityContext->getToken()->getUser();
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                
                $aclProvider = $this->get('security.acl.provider');
                $objectIdentity = ObjectIdentity::fromDomainObject($vial);
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
                
                return $this->redirect($this->generateUrl('flyvial_show',array('id' => $vial->getId())));
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit vial
     * 
     * @param integer $id
     * @return mixed
     * 
     * @Route("/vials/edit/{id}", name="flyvial_edit")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */
    public function editAction($id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);
        
        $form = $this->get('form.factory')->create(new FlyVialType(), $vial);
        
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
     * @param integer $id
     * @return mixed
     * 
     * @Route("/vials/delete/{id}", name="flyvial_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     */
    public function deleteAction($id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vial = $em->find('VIBFliesBundle:FlyVial', $id);

        $em->remove($vial);
        $em->flush();
        return $this->redirect($this->generateUrl('flyvial_listfilter'));
    }
    
    /**
     * Add vial label to PDF
     * 
     * @param Tecnick\TCPDF\TCPDF $pdf
     * @param integer $barcode
     * @param datetime $date
     * @param string $text
     * @return Tecnick\TCPDF\TCPDF
     */    
    private function addFlyLabel(TCPDF $pdf,$barcode,$date,$text) {
        $pdf->AddPage();
        $pdf->write2DBarcode(
                sprintf("%06d",$barcode),
                'QRCODE,H',
                6,2,12.5,12.5,
                $this->get2DBarcodeStyle());
        $pdf->StartTransform();
        $pdf->Rotate(270,39.8,19.1);
        $pdf->write1DBarcode(
                sprintf("%06d",$barcode),
                'C128C',
                22.7,13.1,15.1,4,'',
                $this->get1DBarcodeStyle(),'N');
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
    
    /**
     * Generate label PDF
     * 
     * @return Tecnick\TCPDF\TCPDF
     */ 
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
