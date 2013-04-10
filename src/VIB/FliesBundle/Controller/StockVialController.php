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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use VIB\FliesBundle\Form\StockVialType;
use VIB\FliesBundle\Form\StockVialNewType;


/**
 * StockVialController class
 * 
 * @Route("/stocks/vials")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockVialController extends VialController {

    /**
     * Construct StockVialController
     * 
     */
    public function __construct()
    {
        $this->entityClass = 'VIB\FliesBundle\Entity\StockVial';
        $this->entityName  = 'stock vial|stock vials';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getCreateForm() {
        return new StockVialNewType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new StockVialType();
    }
    
    /**
     * Create new vial (of a stock)
     *
     * @Route("/new/{id}", defaults={"id" = null})
     * @Template()
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction($id = null) {
        $request = $this->getRequest();
        if ($request->getMethod() != 'POST') {
            $class = $this->getEntityClass();
            $vial = new $class();
            if (null !== $id) {
                $stock = $this->getStockEntity($id);
                $vial->setStock($stock);
            }
            $data = array('vial' => $vial, 'number' => 1);
            $form = $this->createForm($this->getCreateForm(), $data);
            return array('form' => $form->createView());
        } else {
            return parent::createAction();
        }
    }
    
    /**
     * Get stock entity
     * 
     * @param mixed $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \VIB\BaseBundle\Entity\Entity
     */
    protected function getStockEntity($id) {
        $class = 'VIB\FliesBundle\Entity\Stock';        
        if ($id instanceof $class) {
            return $id;
        }
        $om = $this->get('vib.doctrine.manager');
        $entity = $om->getRepository($class)->find($id);
        if ($entity instanceof $class) {
            return $entity;
        } else {
            throw new NotFoundHttpException();
        }
        return null;
    }
}
