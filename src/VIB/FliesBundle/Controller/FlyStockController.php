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

use VIB\FliesBundle\Entity\FlyStock;
use VIB\FliesBundle\Form\FlyStockType;

/**
 * FlyStockController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyStockController extends CRUDController
{
    /**
     * Construct FlyStockController
     * 
     */ 
    public function __construct()
    {
        $this->entityClass = 'VIBFliesBundle:FlyStock';
    }
    
    /**
     * List stocks
     * 
     * @Route("/stocks/", name="flystock_list")
     * @Route("/stocks/page/{page}", name="flystock_listpage")
     * @Template()
     * 
     * @param integer $page
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        $response = parent::baseListAction($page);
        return array('stocks' => $response['entities'],
                     'pager' => $response['pager']);
    }
    
    /**
     * Show existing stock
     * 
     * @Route("/stocks/show/{id}", name="flystock_show")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $response = parent::baseShowAction($id);
        return array('stock' => $response['entity']);
    }
    
    
    /**
     * Create new stock
     * 
     * @Route("/stocks/new", name="flystock_create")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $response = parent::baseCreateAction(new FlyStock(), new FlyStockType());
        
        if (isset($response['redirect'])) {
            $url = $this->generateUrl('flystock_show',array('id' => $response['entity']->getId()));
            return $this->redirect($url);
        } else {
            return array(
                'stock' => $response['entity'],
                'form' => $response['form']);
        }
    }

    /**
     * Edit existing stock
     * 
     * @Route("/stocks/edit/{id}", name="flystock_edit")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $response = parent::baseEditAction($id, new FlyStockType());
        
        if (isset($response['redirect'])) {
            $url = $this->generateUrl('flystock_show',array('id' => $response['entity']->getId()));
            return $this->redirect($url);
        } else {
            return array(
                'stock' => $response['entity'],
                'form' => $response['form']);
        }
    }

    /**
     * Delete existing stock
     * 
     * @Route("/stocks/delete/{id}", name="flystock_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        parent::baseDeleteAction($id);
        return $this->redirect($this->generateUrl('flystock_list'));
    }
    
    /**
     * Cascade ACL setting for stock vials
     * 
     * @param Object $entity
     * @param UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($stock, $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        parent::setACL($stock, $user, $mask);
        
        foreach ($stock->getVials() as $vial) {
            parent::setACL($vial, $user, $mask);
        }
    }
}
