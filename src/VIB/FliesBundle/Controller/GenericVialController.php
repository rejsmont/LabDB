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
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Helpers\PDFLabel;
use VIB\FliesBundle\Entity\FlyVial;

/**
 * GenericVialController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class GenericVialController extends CRUDController {
    
    /**
     * Handle batch action
     * 
     * @param string $action
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function handleBatchAction($action, $vials) {
        return null;
    }
    
    /**
     * Handle vial selection form
     * 
     * @param 
     * @return Symfony\Component\HttpFoundation\Response
     */   
    public function handleSelectForm(AbstractType $formType) {
        
        $form = $this->createForm($formType);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $formData = $form->getData();
                return array('response' => $this->handleBatchAction($formData['action'], $formData['items']));
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
    public function generateLabels($vials) {
        
        $entityManager = $this->getEntityManager();
        $pdf = new PDFLabel();
        
        foreach ($vials as $vial) {
            $vial->setLabelPrinted(true);
            $entityManager->persist($vial);
            $pdf->addFlyLabel($vial->getId(), $vial->getSetupDate(), $vial->getLabelText());
        }
        
        $entityManager->flush();
        
        return $pdf->output();
    }
    
    /**
     * Flip vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */     
    public function flipVials($vials) {
        
        $entityManager = $this->getEntityManager();

        $newvials = new ArrayCollection();
        
        foreach ($vials as $vial) {       
            $newvial = new FlyVial($vial);
            $newvials->add($newvial);
            $entityManager->persist($newvial);
        }
        
        $entityManager->flush();
        
        foreach ($newvials as $vial) {
            $this->setACL($vial);
        }
    }
    
    /**
     * Trash vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     */  
    public function trashVials($vials) {
        
        $entityManager = $this->getEntityManager();
        
        foreach ($vials as $vial) {
            $vial->setTrashed(true);
            $entityManager->persist($vial);
        }
        
        $entityManager->flush();        
    }
}
