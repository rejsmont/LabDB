<?php

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Form\CrossVialType;
use VIB\FliesBundle\Form\CrossVialNewType;

use VIB\FliesBundle\Entity\CrossVial;


/**
 * StockVialController class
 * 
 * @Route("/crosses")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialController extends VialController
{
    
    /**
     * Construct CrossVialController
     */ 
    public function __construct() {
        $this->entityClass = 'VIB\FliesBundle\Entity\CrossVial';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getCreateForm() {
        return new CrossVialNewType();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getEditForm() {
        return new CrossVialType();
    }

    /**
     * Create cross
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        
        $cross = new CrossVial();
        $data = array('cross' => $cross, 'number' => 1);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->getCreateForm(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $cross = $data['cross'];
                $number = $data['number'];
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                $crosses = new ArrayCollection();
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                }
                
                for ($i = 0; $i < $number; $i++) {
                    $newcross = new CrossVial($cross);
                    if ($shouldPrint) {
                        $pdf->addFlyLabel($cross->getId(), $cross->getSetupDate(), $cross->getLabelText());
                    }
                    $crosses->add($newcross);
                }
                
                if ($shouldPrint) {
                    $printResult = $this->submitPrintJob($pdf, count($crosses));
                } else {
                    $printResult = false;
                }
                
                foreach($crosses as $cross) {
                    if ($printResult) {
                        $cross->setLabelPrinted(true);
                    }
                    $em->persist($cross);
                }
                $em->flush();

                foreach($crosses as $cross) {
                    $this->setACL($cross);
                }
                
                $url = $number == 1 ? 
                    $this->generateUrl('vib_flies_crossvial_show',array('id' => $cross->getId())) : 
                    $this->generateUrl('vib_flies_crossvial_list');

                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }
}
