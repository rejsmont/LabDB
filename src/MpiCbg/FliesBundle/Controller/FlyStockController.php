<?php

namespace MpiCbg\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MpiCbg\FliesBundle\Entity\FlyStock;
use MpiCbg\FliesBundle\Wrapper\Barcode\FlyStock as FlyStockBarcode;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelector;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelectorItem;
use MpiCbg\FliesBundle\Form\FlyStockBarcodeType;
use MpiCbg\FliesBundle\Form\CollectionSelectorType;

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
        $stocks = $em->getRepository('MpiCbgFliesBundle:FlyStock')->findAll();
        $stocksSelector = new CollectionSelector($stocks);
        
        $form = $this->get('form.factory')
                     ->create(new CollectionSelectorType(), $stocksSelector);
        
        return array('stocks' => $stocksSelector,
                     'form' => $form->createView());
    }
    
    /**
     * Show existing stock
     * 
     * @Route("/stocks/show/{id}", name="flystock_show")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyStock")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('MpiCbgFliesBundle:FlyStock', $id);
        
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
        $stockBarcode = new FlyStockBarcode($em, $stock);

        $form = $this->get('form.factory')
                ->create(new FlyStockBarcodeType(), $stockBarcode);

        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($stock);
                foreach ($stock->getBottles() as $bottle) {
                    $em->persist($bottle);
                }
                $em->flush();
                return $this->redirect($this->generateUrl('flystock_show',array('id' => $stock->getId())));
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit existing stock
     * 
     * @Route("/stocks/edit/{id}", name="flystock_edit")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyStock")
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('MpiCbgFliesBundle:FlyStock', $id);
        $stockBarcode = new FlyStockBarcode($em, $stock);

        $form = $this->get('form.factory')
                ->create(new FlyStockBarcodeType(), $stockBarcode);

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
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyStock")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $stock = $em->find('MpiCbgFliesBundle:FlyStock', $id);

        $em->remove($stock);
        $em->flush();
        return $this->redirect($this->generateUrl('flystock_list'));
    }
}
