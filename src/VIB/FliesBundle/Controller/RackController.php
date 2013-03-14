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

use VIB\BaseBundle\Controller\CRUDController;

use VIB\FliesBundle\Utils\PDFLabel;

use VIB\FliesBundle\Form\RackType;
use VIB\FliesBundle\Form\SelectType;

use VIB\FliesBundle\Entity\Rack;

/**
 * RackController class
 * 
 * @Route("/racks")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RackController extends CRUDController
{
    /**
     * Construct StockController
     * 
     */ 
    public function __construct()
    {
        $this->entityClass = 'VIB\FliesBundle\Entity\Rack';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new RackType();
    }
    
    /**
     * Show rack
     * 
     * @Route("/show/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id) {
        $response = parent::showAction($id);
        
        $form = $this->createForm(new SelectType('VIB\FliesBundle\Entity\Vial'));
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $action = $request->request->get('select_action');
            $selectResponse = $this->forward('VIBFliesBundle:Vial:select');
            if (($action == 'flip')||($action == 'label')||($selectResponse->getStatusCode() >= 400)) {
                return $selectResponse;
            }
        }
        
        return is_array($response) ? array_merge($response, array('form' => $form->createView())) : $response;
    }
    
    /**
     * Create rack
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        
        $rack = new Rack();
        $data = array('rack' => $rack, 'rows' => 10, 'columns' => 10);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $rack = $data['rack'];
                $rows = $data['rows'];
                $columns = $data['columns'];
                
                $rack->setGeometry($rows, $columns);
                
                $em->persist($rack);
                $em->flush();

                $this->setACL($rack);
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    return $this->printLabelAction($rack);
                } else {
                    $url = $this->generateUrl('vib_flies_rack_show',array('id' => $rack->getId())); 
                    return $this->redirect($url);
                }
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Edit rack
     * 
     * @Route("/edit/{id}")
     * @Template()
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $rack = $this->getEntity($id);
        $securityContext = $this->get('security.context');
        
        if (!($securityContext->isGranted('ROLE_ADMIN')||$securityContext->isGranted('EDIT', $rack))) {
            throw new AccessDeniedException();
        }        
        
        $data = array('rack' => $rack, 'rows' => $rack->getRows(), 'columns' => $rack->getColumns());
        
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $rack = $data['rack'];
                $rows = $data['rows'];
                $columns = $data['columns'];
                
                $rack->setGeometry($rows, $columns);
                
                $em->persist($rack);
                $em->flush();
                
                $url = $this->generateUrl('vib_flies_rack_show',array('id' => $rack->getId())); 
                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Prepare label
     * 
     * @param VIB\FliesBundle\Entity\Rack $rack
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function prepareLabel(Rack $rack) {
        $pdf = $this->get('vibfolks.pdflabel');
        $pdf->addRackLabel($rack->getId(), $rack->getLabelText());
        return $pdf;
    }

    /**
     * Generate rack label
     * 
     * @Route("/label/{id}/download")
     * 
     * @param mixed $id
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function downloadLabelAction($id) {
        $rack = $this->getEntity($id);        
        $pdf = $this->prepareLabel($rack);
        return $pdf->output();
    }
    
    /**
     * Print rack label
     * 
     * @Route("/label/{id}/print")
     * 
     * @param mixed $id
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function printLabelAction($id) {
        $rack = $this->getEntity($id);
        $pdf = $this->prepareLabel($rack);
        $jobStatus = $pdf->printPDF();
        if ($jobStatus == 'successfull-ok') {
            $this->get('session')->getFlashBag()
                 ->add('success', 'Label was sent to the printer. ');
        } else {
            $this->get('session')->getFlashBag()
                 ->add('error', 'There was an error printing labels. The print server said: ' . $jobStatus);
        }
        
        $url = $this->generateUrl('vib_flies_rack_show',array('id' => $rack->getId()));
        return $this->redirect($url);
    }
    
}
