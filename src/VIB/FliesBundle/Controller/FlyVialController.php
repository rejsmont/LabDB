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

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Form\FlyVialType;
use VIB\FliesBundle\Form\FlyVialSelectType;

/**
 * FlyVialController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyVialController extends GenericVialController {

    /**
     * Construct FlyVialController
     * 
     */ 
    public function __construct()
    {
        $this->entityClass = 'VIBFliesBundle:FlyVial';
    }
    
    /**
     * List vials
     * 
     * @Route("/vials", name="flyvial_list")
     * @Route("/vials/page/{page}", name="flyvial_listpage")
     * @Template()
     * 
     * @param integer $page
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        $query = $this->getEntityManager()
                      ->getRepository($this->getEntityClass())
                      ->findAllLivingStocksQuery();
        
        $response = parent::baseListAction($page,$query);
        $formResponse = $this->handleSelectForm(new FlyVialSelectType());
        
        if (isset($formResponse['response'])) {
            return $formResponse['response'];
        } else if (isset($formResponse['form'])) {       
            return array(
                'vials' => $response['entities'],
                'form' => $formResponse['form'],
                'pager' => $response['pager']
            );
        }
    }

    /**
     * Select vials
     * 
     * @Route("/vials/select", name="flyvial_select")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function selectAction() {
        
        $formResponse = $this->handleSelectForm(new FlyVialSelectType());
                
        if (isset($formResponse['response'])) {
            return $formResponse['response'];
        } else if (isset($formResponse['form'])) {       
            return array(
                'vials' => null,
                'form' => $formResponse['form'],
                'pager' => null
            );
        }
    }
    
    /**
     * Show vial
     * 
     * @Route("/vials/show/{id}", name="flyvial_show")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id) {
        $response = parent::baseShowAction($id);
        return array('vial' => $response['entity']);
    }    
    
    /**
     * Create new vial
     * 
     * @Route("/vials/new", name="flyvial_create")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        $response = parent::baseCreateAction(new FlyVial(), new FlyVialType());
        
        if (isset($response['redirect'])) {
            $url = $this->generateUrl('flyvial_show',array('id' => $response['entity']->getId()));
            return $this->redirect($url);
        } else {
            return array(
                'vial' => $response['entity'],
                'form' => $response['form']);
        }
    }

    /**
     * Edit vial
     * 
     * @Route("/vials/edit/{id}", name="flyvial_edit")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id) {
        $response = parent::baseEditAction($id, new FlyVialType());
        
        if (isset($response['redirect'])) {
            $url = $this->generateUrl('flyvial_show',array('id' => $response['entity']->getId()));
            return $this->redirect($url);
        } else {
            return array(
                'vial' => $response['entity'],
                'form' => $response['form']);
        }
    }

    /**
     * Delete vial
     * 
     * @Route("/vials/delete/{id}", name="flyvial_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id) {
        parent::baseDeleteAction($id);
        return $this->redirect($this->generateUrl('flyvial_list'));
    }
    
    /**
     * Handle batch action
     * 
     * @param string $action
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function handleBatchAction($action, $vials) {
        
        switch($action) {
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
     * Flip vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */     
    public function flipVials($vials) {
        parent::flipVials($vials);
        return $this->redirect($this->generateUrl('flyvial_list'));
    }
    
    /**
     * Trash vials
     * 
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */  
    public function trashVials($vials) {
        parent::trashVials($vials);
        return $this->redirect($this->generateUrl('flyvial_list'));
    }
}
