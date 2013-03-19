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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Form\CrossVialType;
use VIB\FliesBundle\Form\CrossVialNewType;

use VIB\FliesBundle\Entity\CrossVial;


/**
 * StockVialController class
 * 
 * @Route("/crosses")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialController extends VialController
{
    
    /**
     * Construct CrossVialController
     */ 
    public function __construct() {
        $this->entityClass = 'VIB\FliesBundle\Entity\CrossVial';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getCreateForm() {
        return new CrossVialNewType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new CrossVialType();
    }

    /**
     * Create cross
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        
        $cross = new CrossVial();
        $data = array('cross' => $cross, 'number' => 1);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $cross = $data['cross'];
                $number = $data['number'];
                
                $crosses = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $newcross = new CrossVial($cross);
                    $em->persist($newcross);
                    $crosses->add($newcross);
                }
                $em->flush();
                
                foreach($crosses as $cross) {
                    $this->setACL($cross);
                }
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    
                    foreach($crosses as $cross) {
                        $pdf->addFlyLabel($cross->getId(), $cross->getSetupDate(),
                                          $cross->getLabelText(), $this->getOwner($cross));
                    }
                    if ($this->submitPrintJob($pdf, count($crosses))) {
                        foreach($crosses as $cross) {
                            $cross->setLabelPrinted(true);
                            $em->persist($cross);
                        }
                        $em->flush();
                    }
                }
                
                $url = $number == 1 ? 
                    $this->generateUrl('vib_flies_crossvial_show',array('id' => $cross->getId())) : 
                    $this->generateUrl('vib_flies_crossvial_list');

                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleBatchAction($data) {
        
        $action = $data['action'];
        $vials = $data['items'];
        
        $response = $this->getDefaultBatchResponse();
        
        switch($action) {
            case 'marksterile':
                $this->markSterile($vials);
                break;
            default:
                return parent::handleBatchAction($data);
        }
        
        return $response;
    }
    
    /**
     * Mark crosses as sterile and trash them
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function markSterile(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            if ($vial instanceof CrossVial) {
                $vial->setSterile(true);
                $em->persist($vial);
            }
        }
        
        $em->flush();
    }
}
