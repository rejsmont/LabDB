<?php

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\FlyCross;
use VIB\FliesBundle\Entity\FlyVial;
use VIB\FliesBundle\Form\FlyCrossType;
use VIB\FliesBundle\Form\FlyCrossNewType;
use VIB\FliesBundle\Form\FlyCrossSelectType;

class FlyCrossController extends VialController
{
    
    /**
     * Construct FlyCrossController
     */ 
    public function __construct() {
        $this->entityClass = 'VIB\FliesBundle\Entity\FlyCross';
    }
    
    /**
     * List crosses
     * 
     * @Route("/crosses/", name="flycross_list")
     * @Route("/crosses/page/{page}", name="flycross_listpage")
     * @Template()
     * 
     * @param integer $page
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        $query = $this->getDoctrine()->getManager()
                      ->getRepository($this->getEntityClass())
                      ->findAllLivingQuery();
        
        return $this->getListResponse($page,$query);
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
     * Show cross
     * 
     * @Route("/crosses/show/{id}", name="flycross_show")
     * @Template()
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial",
     *     options={"error_message": "Cross vial %s does not exist"})
     * 
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction(FlyVial $vial) {
        if (null !== $vial->getCross()) {
            return $this->getShowResponse($vial->getCross());
        } else {
            throw $this->createNotFoundException();
        }
    }
    
    /**
     * Create new cross
     * 
     * @Route("/crosses/new", name="flycross_create")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction() {
        
        $cross = new FlyCross();
        $data = array('cross' => $cross, 'number' => 1);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new FlyCrossNewType(), $data);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $cross = $data['cross'];
                $number = $data['number'];
                
                $crosses = new ArrayCollection();
                
                for ($i = 0; $i < $number; $i++) {
                    $newcross = new FlyCross($cross);
                    $em->persist($newcross);
                    $crosses->add($newcross);
                }
                
                $em->flush();

                foreach($crosses as $cross) {
                    $this->setACL($cross);
                }
                
                $url = $number == 1 ? 
                    $this->generateUrl('flycross_show',array('id' => $cross->getRoutableId())) : 
                    $this->generateUrl('flycross_list');

                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit cross
     * 
     * @Route("/crosses/edit/{id}", name="flycross_edit")
     * @Template()
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial")
     * 
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(FlyVial $vial) {
        if (null !== $vial->getCross()) {
            return $this->getEditResponse($vial->getCross(), new FlyCrossType(), 'flycross_show');
        } else {
            throw $this->createNotFoundException();
        }
    }

    /**
     * Delete cross (and its vial)
     * 
     * @Route("/crosses/delete/{id}", name="flycross_delete")
     * @Template()
     * @ParamConverter("vial", class="VIBFliesBundle:FlyVial")
     * 
     * @param VIB\FliesBundle\Entity\FlyVial $vial
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(FlyVial $vial) {
        if (null !== $vial->getCross()) {
            return $this->getDeleteResponse($vial->getCross(), 'flycross_list');
        } else {
            throw $this->createNotFoundException();
        }
    }
    
    /**
     * Cascade ACL setting for cross vial
     * 
     * @param VIB\FliesBundle\Entity\FlyCross $cross
     * @param Symfony\Component\Security\Core\User\UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($cross, UserInterface $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        parent::setACL($cross, $user, $mask);
        $vial = $cross->getVial();
        if (null !== $vial) {
            parent::setACL($vial, $user, $mask);
        }
    }
    
    /**
     * Handle batch action
     * 
     * @param string $action
     * @param Doctrine\Common\Collections\Collection $vials
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function handleBatchAction($data) {
        
        switch($action) {
            case 'label':
                return $this->generateLabels($vials);
                break;
            case 'trash':
                return $this->trashVials($vials);
                break;
            default:
                return $this->redirect($this->generateUrl('flyvial_list'));
                break;
        }
    }
    
    /**
     * Trash vials
     * 
     * @param Doctrine\Common\Collections\Collection $crosses
     * @return Symfony\Component\HttpFoundation\Response
     */  
    public function trashVials(Collection $crosses) {
        
        $vials = new ArrayCollection();
        
        foreach ($crosses as $cross) {
            $vials->add($cross->getVial());
        }
        
        parent::trashVials($vials);
        return $this->redirect($this->generateUrl('flycross_list'));
    }
    
    /**
     * Generate vial labels
     * 
     * @param Doctrine\Common\Collections\Collection $crosses
     * @return Symfony\Component\HttpFoundation\Response
     */  
    public function generateLabels(Collection $crosses) {
        
        $vials = new ArrayCollection();
        
        foreach ($crosses as $cross) {
            $vials->add($cross->getVial());
        }
        
        return parent::generateLabels($vials);
    }
}
