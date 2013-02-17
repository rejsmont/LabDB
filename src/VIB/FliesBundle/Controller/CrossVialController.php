<?php

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\CrossVial;
use VIB\FliesBundle\Entity\Vial;
use VIB\FliesBundle\Form\CrossVialType;
use VIB\FliesBundle\Form\CrossVialNewType;

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
     * Select crosses
     * 
     * @Route("/crosses/select", name="flycross_select")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function selectAction() {
        
        $formResponse = $this->handleSelectForm(new FlyCrossSelectType());
                
        if (isset($formResponse['response'])) {
            return $formResponse['response'];
        } else if (isset($formResponse['form'])) {       
            return array(
                'crosses' => null,
                'form' => $formResponse['form'],
                'pager' => null
            );
        }
    }

    /**
     * Create cross
     * 
     * @Route("/new")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
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
