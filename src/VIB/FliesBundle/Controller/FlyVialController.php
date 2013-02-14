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

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Form\FlyVialType;
use VIB\FliesBundle\Form\FlyVialExpandType;
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
        $this->entityName = 'vial';
    }
    
    /**
     * List vials
     * 
     * @Route("/vials/", name="flyvial_list")
     * @Route("/vials/page/{page}", name="flyvial_listpage")
     * @Template()
     * 
     * @param integer $page
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        $query = $this->getDoctrine()->getManager()
                      ->getRepository($this->getEntityClass())
                      ->findAllLivingStocksQuery();
        
        $response = parent::baseListAction($page,$query);
       // $formResponse = $this->handleSelectForm(new FlyVialSelectType());
        
//        if (isset($formResponse['response'])) {
//            return $formResponse['response'];
//        } else if (isset($formResponse['form'])) {       
//            return array(
//                'vials' => $response['entities'],
//                'form' => $formResponse['form']
//            );
//        }
        return array('vials' => $response['entities']);
    }

    /**
     * List created vials
     * 
     * @param integer $vials
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listCreated($vials)
    {
        $formResponse = $this->handleSelectForm(new FlyVialSelectType());
        
        if (isset($formResponse['response'])) {
            return $formResponse['response'];
        } else if (isset($formResponse['form'])) {       
            return array(
                'vials' => $vials,
                'form' => $formResponse['form'],
                'pager' => null
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
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial")
     * 
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction(FlyVial $vial) {
                
        if (null !== $vial->getCross()) {
            return $this->forward('VIBFliesBundle:FlyCross:show', array('vial'  => $vial));
        } else {
            return parent::baseShowAction($vial);
        }
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
        return parent::baseCreateAction(new FlyVial(), new FlyVialType(), 'flyvial_show');
    }

    /**
     * Edit vial
     * 
     * @Route("/vials/edit/{id}", name="flyvial_edit")
     * @Template()
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial")
     * 
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(FlyVial $vial) {

        if (null !== $vial->getCross()) {
            return $this->forward('VIBFliesBundle:FlyCross:edit', array('vial'  => $vial));
        } else {
            return parent::baseEditAction($vial, new FlyVialType(), 'flyvial_show');
        }
    }

    /**
     * Delete vial
     * 
     * @Route("/vials/delete/{id}", name="flyvial_delete")
     * @Template()
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(FlyVial $vial) {
        
        if (null !== $vial->getCross()) {
            return $this->forward('VIBFliesBundle:FlyCross:delete', array('vial'  => $vial));
        } else {
            return parent::baseDeleteAction($vial, 'flyvial_list');
        }
    }
    
    /**
     * Expand vial
     * 
     * @Route("/vials/expand/", name="flyvial_expand", defaults={"id" = 0})
     * @Route("/vials/expand/{id}", name="flyvial_expand_id")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyVial")
     * 
     * @param integer $id
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function expandAction($id = null) {

        $entityManager = $this->getEntityManager();

        if ($id > 0) {
            $source = $entityManager->find($this->getEntityClass(), $id);
        } else {
            $source = null;
        }
        
        $data = array('source' => $source, 'number' => 1);
        
        $form = $this->getFormFactory()->create(new FlyVialExpandType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $source = $data['source'];
                $number = $data['number'];
                
                $vials = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $newvial = new FlyVial($source);
                    $entityManager->persist($newvial);
                    $vials->add($newvial);
                }
                
                $entityManager->flush();

                foreach($vials as $vial) {
                    $this->setACL($vial);
                }
                
                $url = $this->generateUrl('flyvial_list');
                return $this->redirect($url);
            }
        }
        
        return array(
            'source' => $source,
            'form' => $form->createView());
        
        //return $this->redirect($this->generateUrl('flyvial_list'));
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
