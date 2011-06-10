<?php

namespace MpiCbg\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MpiCbg\FliesBundle\Entity\FlyCross;
use MpiCbg\FliesBundle\Wrapper\Barcode\FlyCross as FlyCrossBarcode;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelector;
use MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelectorItem;
use MpiCbg\FliesBundle\Form\FlyCrossBarcodeType;
use MpiCbg\FliesBundle\Form\CollectionSelectorType;

class FlyCrossController extends Controller
{
    /**
     * List crosses
     * 
     * @Route("/crosses/", name="flycross_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $crosses = $em->getRepository('MpiCbgFliesBundle:FlyCross')->findAll();
        $crossesSelector = new CollectionSelector($crosses);
        
        $form = $this->get('form.factory')
                     ->create(new CollectionSelectorType(), $crossesSelector);
        
        return array('crosses' => $crossesSelector,
                     'form' => $form->createView());
    }
    
    /**
     * Show cross
     * 
     * @Route("/crosses/show/{id}", name="flycross_show")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyCross")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('MpiCbgFliesBundle:FlyCross', $id);
        
        return array('cross' => $cross);
    }
    
    
    /**
     * Create new cross
     * 
     * @Route("/crosses/new", name="flycross_create")
     * @Template()
     */
    public function createAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = new FlyCross();
        $crossBarcode = new FlyCrossBarcode($em, $cross);
        
        $form = $this->get('form.factory')
                ->create(new FlyCrossBarcodeType(), $crossBarcode);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($cross);
                $em->persist($cross->getBottle());
                $em->flush();
                return $this->redirect($this->generateUrl('flycross_show',array('id' => $cross->getId())));
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit cross
     * 
     * @Route("/crosses/edit/{id}", name="flycross_edit")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyCross")
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('MpiCbgFliesBundle:FlyCross', $id);
        $crossBarcode = new FlyCrossBarcode($em, $cross);
        
        $form = $this->get('form.factory')
                ->create(new FlyCrossBarcodeType(), $crossBarcode);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($cross);
                $em->persist($cross->getBottle());
                $em->flush();
                return $this->redirect($this->generateUrl('flycross_show',array('id' => $cross->getId())));
            }
        }
        
        return array('cross' => $cross,
                     'form' => $form->createView());
    }

    /**
     * Delete cross (and its bottle)
     * 
     * @Route("/crosses/delete/{id}", name="flycross_delete")
     * @Template()
     * @ParamConverter("id", class="MpiCbgFliesBundle:FlyCross")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('MpiCbgFliesBundle:FlyCross', $id);

        $em->remove($cross->getBottle());
        $em->remove($cross);
        $em->flush();
        return $this->redirect($this->generateUrl('flycross_list'));
    }
}
