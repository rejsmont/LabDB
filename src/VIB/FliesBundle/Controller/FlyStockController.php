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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use VIB\FliesBundle\Entity\FlyStock;
use VIB\FliesBundle\Form\FlyStockType;

/**
 * FlyStockController class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyStockController extends Controller
{
    /**
     * List stocks
     * 
     * @Route("/stocks/", name="flystock_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stocks = $em->getRepository('VIBFliesBundle:FlyStock')->findAll();
        
        return array('stocks' => $stocks);
    }
    
    /**
     * Show existing stock
     * 
     * @Route("/stocks/show/{id}", name="flystock_show")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('VIBFliesBundle:FlyStock', $id);
        
        return array('stock' => $stock);
    }
    
    
    /**
     * Create new stock
     * 
     * @Route("/stocks/new", name="flystock_create")
     * @Template()
     */
    public function createAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = new FlyStock();

        $form = $this->get('form.factory')
                ->create(new FlyStockType(), $stock);

        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($stock);
                foreach ($stock->getVials() as $vial) {
                    $em->persist($vial);
                }
                $em->flush();
                
                $securityContext = $this->get('security.context');
                $user = $securityContext->getToken()->getUser();
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                
                $aclProvider = $this->get('security.acl.provider');
                
                $objectIdentity = ObjectIdentity::fromDomainObject($stock);
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
                
                foreach ($stock->getVials() as $vial) {
                    $objectIdentity = ObjectIdentity::fromDomainObject($vial);
                    $acl = $aclProvider->createAcl($objectIdentity);
                    $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                    $aclProvider->updateAcl($acl);
                }
                
                return $this->redirect($this->generateUrl('flystock_show',array('id' => $stock->getId())));
            }
        }
        
        return array(
            'stock' => $stock,
            'form' => $form->createView());
    }

    /**
     * Edit existing stock
     * 
     * @Route("/stocks/edit/{id}", name="flystock_edit")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('VIBFliesBundle:FlyStock', $id);

        $form = $this->get('form.factory')
                ->create(new FlyStockType(), $stock);

        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($stock);
                $em->flush();
                return $this->redirect($this->generateUrl('flystock_show',array('id' => $stock->getId())));
            }
        }
        
        return array(
            'stock' => $stock,
            'form' => $form->createView());
    }

    /**
     * Delete existing stock
     * 
     * @Route("/stocks/delete/{id}", name="flystock_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyStock")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('VIBFliesBundle:FlyStock', $id);

        $em->remove($stock);
        $em->flush();
        return $this->redirect($this->generateUrl('flystock_list'));
    }
}
