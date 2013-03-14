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

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use VIB\BaseBundle\Controller\CRUDController;

use VIB\FliesBundle\Utils\PDFLabel;

use VIB\FliesBundle\Form\StockType;
use VIB\FliesBundle\Form\StockVialType;

use VIB\FliesBundle\Entity\Stock;
use VIB\FliesBundle\Entity\StockVial;


/**
 * StockController class
 * 
 * @Route("/stocks")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockController extends CRUDController
{
    /**
     * Construct StockController
     * 
     */ 
    public function __construct()
    {
        $this->entityClass = 'VIB\FliesBundle\Entity\Stock';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new StockType();
    }
    
    /**
     * Create stock
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        $em = $this->getDoctrine()->getManager();
        $class = $this->getEntityClass();
        $stock = new $class();
        $existingStock = null;
        $form = $this->createForm($this->getCreateForm(), $stock);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $em->persist($stock);
                $em->flush();
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    $vials = $stock->getVials();
                    foreach ($vials as $vial) {
                        $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
                    }
                    if ($this->submitPrintJob($pdf)) {
                        foreach ($vials as $vial) {
                            $vial->setLabelPrinted(true);
                            $em->persist($vial);
                        }
                        $em->flush();
                    }
                }
                
                $this->setACL($stock);
                
                $route = str_replace("_create", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route,array('id' => $stock->getId()));
                return $this->redirect($url);
            } elseif ($stock instanceof Stock) {
                $existingStock = $em->getRepository($this->getEntityClass())
                        ->findOneBy(array('name' => $stock->getName()));
            }
        }
        return array('form' => $form->createView(), 'existingStock' => $existingStock);
    }
    
    /**
     * Create new vial of a stock
     *
     * @Route("/new/vial/{id}")
     * @Template()
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function newVialAction($id) {
        $em = $this->getDoctrine()->getManager();
        $stock = $this->getEntity($id);
        $vial = new StockVial();
        $vial->setStock($stock);
        $form = $this->createForm(new StockVialType(), $vial);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $em->persist($vial);
                $em->flush();
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
                    if ($this->submitPrintJob($pdf)) {
                        $vial->setLabelPrinted(true);
                        $em->persist($vial);
                        $em->flush();
                    }
                }
                
                parent::setACL($vial);
                
                $url = $this->generateUrl('vib_flies_stockvial_show',array('id' => $vial->getId()));
                return $this->redirect($url);
            }
        }
        return array('form' => $form->createView());
    }
    
    /**
     * Submit print job
     * 
     * @param VIB\FliesBundle\Utils\PDFLabel $pdf
     * @param integer $count
     * @return boolean
     */
    public function submitPrintJob(PDFLabel $pdf, $count = 1) {
        $jobStatus = $pdf->printPDF();
        if ($jobStatus == 'successfull-ok') {
            if ($count == 1) {
                $this->get('session')->getFlashBag()
                     ->add('success', 'Label for 1 vial was sent to the printer.');
            } else {
                $this->get('session')->getFlashBag()
                     ->add('success', 'Labels for ' . $count . ' vials were sent to the printer. ');
            }
            return true;
        } else {
            $this->get('session')->getFlashBag()
                 ->add('error', 'There was an error printing labels. The print server said: ' . $jobStatus);
            return false;
        }
    }
    
    /**
     * Cascade ACL setting for stock vials
     * 
     * @param Object $entity
     * @param \Symfony\Component\Security\Core\User\UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($entity, UserInterface $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        parent::setACL($entity, $user, $mask);
        
        foreach ($entity->getVials() as $vial) {
            parent::setACL($vial, $user, $mask);
        }
    }
}
