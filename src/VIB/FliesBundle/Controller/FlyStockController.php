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
        $this->entityClass = 'VIB\FliesBundle\Entity\FlyStock';
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
        return $this->getListResponse($page);
    }
    
    /**
     * Show existing stock
     * 
     * @Route("/stocks/show/{id}", name="flystock_show")
     * @Template()
     * @ParamConverter("stock", class="VIBFliesBundle:FlyStock")
     * 
     * @param VIB\FliesBundle\Entity\FlyStock $stock
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction(FlyStock $stock)
    {
        return $this->getShowResponse($stock);
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
        return $this->getCreateResponse(new FlyStock(), new FlyStockType(), 'flystock_show');
    }

    /**
     * Edit existing stock
     * 
     * @Route("/stocks/edit/{id}", name="flystock_edit")
     * @Template()
     * @ParamConverter("stock", class="VIBFliesBundle:FlyStock")
     * 
     * @param VIB\FliesBundle\Entity\FlyStock $stock
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(FlyStock $stock)
    {
        return $this->getEditResponse($stock, new FlyStockType(), 'flystock_show');
    }

    /**
     * Delete existing stock
     * 
     * @Route("/stocks/delete/{id}", name="flystock_delete")
     * @Template()
     * @ParamConverter("stock", class="VIBFliesBundle:FlyStock")
     * 
     * @param VIB\FliesBundle\Entity\FlyStock $stock
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(FlyStock $stock)
    {
        return $this->getDeleteResponse($stock, 'flystock_list');
    }
    
    /**
     * Cascade ACL setting for stock vials
     * 
     * @param Object $entity
     * @param Symfony\Component\Security\Core\User\UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($entity, UserInterface $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        parent::setACL($entity, $user, $mask);
        
        foreach ($entity->getVials() as $vial) {
            parent::setACL($vial, $user, $mask);
        }
    }
}
