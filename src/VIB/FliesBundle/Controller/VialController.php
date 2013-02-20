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
use JMS\SecurityExtraBundle\Annotation\SatisfiesParentSecurityPolicy;

use Symfony\Component\Form\AbstractType;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\BaseBundle\Controller\CRUDController;

use VIB\FliesBundle\Utils\PDFLabel;

use VIB\FliesBundle\Form\VialType;
use VIB\FliesBundle\Form\VialExpandType;
use VIB\FliesBundle\Form\SelectType;

use VIB\FliesBundle\Entity\Vial;


/**
 * VialController class
 *
 * @Route("/vials")
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialController extends CRUDController {
    
    /**
     * Construct StockVialController
     * 
     */
    public function __construct()
    {
        $this->entityClass  = 'VIB\FliesBundle\Entity\Vial';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getListQuery() {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository($this->getEntityClass())->findAllLivingQuery();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new VialType();
    }
    
        
    /**
     * List vials
     * 
     * @Route("/")
     * @Template()
     * @SatisfiesParentSecurityPolicy
     * 
     * @param integer $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        $response = parent::listAction($page);
        $formResponse = $this->handleSelectForm(new SelectType('VIB\FliesBundle\Entity\Vial'));
        
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Show vial
     * 
     * @Route("/show/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id) {
        $vial = $this->getEntity($id);
        if ($this->controls($vial)) {
            return parent::showAction($vial);
        } else {
            return $this->getVialRedirect($vial);
        }
    }

    /**
     * Edit vial
     * 
     * @Route("/edit/{id}")
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id) {
        $vial = $this->getEntity($id);
        if ($this->controls($vial)) {
            return parent::editAction($vial);
        } else {
            return $this->getVialRedirect($vial);
        }
    }
    
    /**
     * Expand vial
     * 
     * @Route("/expand/{id}", defaults={"id" = null})
     * @Template()
     * 
     * @param mixed $id
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function expandAction($id = null) {

        $em = $this->getDoctrine()->getManager();
        $source = (null !== $id) ? $this->getEntity($id) : null;
        $data = array('source' => $source, 'number' => 1);        
        $form = $this->createForm(new VialExpandType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $source = $data['source'];
                $number = $data['number'];
                
                $vials = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $vial = $source->flip();
                    $em->persist($vial);
                    $vials->add($vial);
                }
                
                $em->flush();

                foreach($vials as $vial) {
                    $this->setACL($vial);
                }
                
                $route = str_replace("_expand", "_list", $request->attributes->get('_route'));
                $url = $this->generateUrl($route);
                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView(), 'cancel' => 'vib_flies_vial_list');
    }
    
    /**
     * Select vials
     * 
     * @Route("/select")
     * @Template()
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectAction() {
        $response = array();
        $formResponse = $this->handleSelectForm(new SelectType('VIB\FliesBundle\Entity\Vial'));
        
        return is_array($formResponse) ? array_merge($response, $formResponse) : $formResponse;
    }
    
    /**
     * Handle batch action
     * 
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleBatchAction($data) {
        
        $action = $data['action'];
        $vials = $data['items'];
        
        $response = $this->getDefaultBatchResponse();
        
        switch($action) {
            case 'label':
                $response = $this->generateLabels($vials);
                break;
            case 'flip':
                $response = $this->flipVials($vials);
                break;
            case 'trash':
                $response = $this->trashVials($vials);
                break;
        }
        
        return $response;
    }
    
    /**
     * Handle selection form
     * 
     * @param \Symfony\Component\Form\AbstractType $formType
     * @return array|\Symfony\Component\HttpFoundation\Response
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
     * @param \Doctrine\Common\Collections\Collection $vials
     * @return \Symfony\Component\HttpFoundation\Response
     */    
    public function generateLabels(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        $pdf = new PDFLabel($this->get('white_october.tcpdf'));
        
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
     * @param \Doctrine\Common\Collections\Collection $vials
     */     
    public function flipVials(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();

        $flippedVials = new ArrayCollection();
        
        foreach ($vials as $source) {
            $vial = $source->flip();
            $em->persist($vial);
            $flippedVials->add($vial);
        }
        
        $em->flush();
        
        foreach ($flippedVials as $vial) {
            parent::setACL($vial);
        }
        
        return $this->getDefaultBatchResponse();
    }
    
    /**
     * Trash vials
     * 
     * @param \Doctrine\Common\Collections\Collection $vials
     */  
    public function trashVials(Collection $vials) {
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($vials as $vial) {
            $vial->setTrashed(true);
            $em->persist($vial);
        }
        
        $em->flush();
        
        return $this->getDefaultBatchResponse();
    }
    
    /**
     * Get default batch action response
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getDefaultBatchResponse() {
        $request = $this->getRequest();
        $pieces = explode('_',$request->attributes->get('_route'));
        $pieces[count($pieces) - 1] = 'list';
        $route = implode('_', $pieces);
        $url = $this->generateUrl($route);
        return $this->redirect($url);
    }
    
    /**
     * 
     * @param \VIB\FliesBundle\Entity\Vial $vial
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getVialRedirect(Vial $vial) {
        $request = $this->getRequest();
        $route = str_replace("_vial_", "_" . $vial->getType() . "vial_", $request->attributes->get('_route'));
        $url = $this->generateUrl($route, array('id' => $vial->getId()));
        return $this->redirect($url);
    }
}
