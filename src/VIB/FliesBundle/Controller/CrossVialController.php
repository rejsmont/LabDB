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
                
                $crosses = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $newcross = new CrossVial($cross);
                    $em->persist($newcross);
                    $crosses->add($newcross);
                }
                $em->flush();
                
                $shouldPrint = $this->get('request')->getSession()->get('autoprint') == 'enabled';
                
                if ($shouldPrint) {
                    $pdf = $this->get('vibfolks.pdflabel');
                    
                    foreach($crosses as $cross) {
                        $pdf->addFlyLabel($cross->getId(), $cross->getSetupDate(),
                                          $cross->getLabelText(), $this->getOwner($cross));
                    }
                    if ($this->submitPrintJob($pdf, count($crosses))) {
                        foreach($crosses as $cross) {
                            $cross->setLabelPrinted(true);
                            $em->persist($cross);
                        }
                        $em->flush();
                    }
                }
                
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
