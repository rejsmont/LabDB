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

use Symfony\Component\Form\AbstractType;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

use VIB\FliesBundle\Form\SelectType;
use VIB\FliesBundle\Helpers\PDFLabel;
use VIB\FliesBundle\Entity\FlyVial;

/**
 * VialController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class VialController extends CRUDController {
    
    /**
     * {@inheritdoc}
     * @return array|Symfony\Component\HttpFoundation\Response
     */
    protected function getListResponse($page = 1, QueryBuilder $query = null, $maxPerPage = 15)
    {        
        $response = parent::getListResponse($page,$query,$maxPerPage);
        $formResponse = $this->handleSelectForm(new SelectType($this->getEntityClass()));
        
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Handle batch action
     * 
     * @param string $action
     * @param array $data
     * @return Symfony\Component\HttpFoundation\Response
     */
    public abstract function handleBatchAction($data);
    
    /**
     * Handle vial selection form
     * 
     * @param Symfony\Component\Form\AbstractType $formType
     * @return array|Symfony\Component\HttpFoundation\Response
     */   
    public function handleSelectForm(AbstractType $formType) {
        
        $form = $this->createForm($formType);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                return $this->handleBatchAction($form->getData());
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * Generate vial labels
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function generateLabels(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        $pdf = new PDFLabel();
        
        foreach ($vials as $vial) {
            $vial->setLabelPrinted(true);
            $em->persist($vial);
            $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
        }
        
        $em->flush();
        
        return $pdf->output();
    }
    
    /**
     * Flip vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     */     
    public function flipVials(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();

        $newvials = new ArrayCollection();
        
        foreach ($vials as $vial) {       
            $newvial = new FlyVial($vial);
            $newvials->add($newvial);
            $em->persist($newvial);
        }
        
        $em->flush();
        
        foreach ($newvials as $vial) {
            $this->setACL($vial);
        }
    }
    
    /**
     * Trash vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     */  
    public function trashVials(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            $vial->setTrashed(true);
            $em->persist($vial);
        }
        
        $em->flush();        
    }
}
