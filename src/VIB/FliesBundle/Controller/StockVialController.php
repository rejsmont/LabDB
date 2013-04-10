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
        $this->entityName  = 'stock';
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
        $om = $this->getObjectManager();
        $class = $this->getEntityClass();
        $vial = new $class;
        if (null !== $id) {
            $stock = $this->getStockEntity($id);
            $vial->setStock($stock);
        }
        $form = $this->createForm($this->getCreateForm(), $vial);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $om->persist($vial);
                $om->flush();
                $om->createACL($vial,$this->getDefaultACL());
                $this->addSessionFlash('success', 'Vial ' . $vial . ' was created.');
                $this->autoPrint($vial);
                $url = $this->generateUrl('vib_flies_stockvial_show',array('id' => $vial->getId()));
                return $this->redirect($url);
            }
        }
        return array('form' => $form->createView());
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
