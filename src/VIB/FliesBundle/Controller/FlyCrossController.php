<?php

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use VIB\FliesBundle\Entity\FlyCross;
use VIB\FliesBundle\Form\FlyCrossType;
use VIB\FliesBundle\Form\FlyCrossNewType;
use VIB\FliesBundle\Form\FlyCrossSelectType;

class FlyCrossController extends GenericVialController
{
    
    /**
     * Construct FlyCrossController
     * 
     */ 
    public function __construct()
    {
        $this->entityClass = 'VIBFliesBundle:FlyCross';
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
        $query = $this->getEntityManager()
                      ->getRepository($this->getEntityClass())
                      ->findAllLivingQuery();
        
        $response = parent::baseListAction($page,$query);
        $formResponse = $this->handleSelectForm(new FlyCrossSelectType());
        
        if (isset($formResponse['response'])) {
            return $formResponse['response'];
        } else if (isset($formResponse['form'])) {       
            return array(
                'crosses' => $response['entities'],
                'form' => $formResponse['form'],
                'pager' => $response['pager']
            );
        }
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
     * @ParamConverter("id", class="VIBFliesBundle:FlyCross")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $response = parent::baseShowAction($id);
        return array('cross' => $response['entity']);
    }
    
    
    /**
     * Create new cross
     * 
     * @Route("/crosses/new", name="flycross_create")
     * @Template()
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $cross = new FlyCross();
        $data = array('cross' => $cross, 'number' => 1);
        
        $entityManager = $this->getEntityManager();
        $form = $this->getFormFactory()->create(new FlyCrossNewType(), $data);
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
                    $entityManager->persist($newcross);
                    $crosses->add($newcross);
                }
                
                $entityManager->flush();

                foreach($crosses as $cross) {
                    $this->setACL($cross);
                }
                
                if ($number == 1)
                    $url = $this->generateUrl('flycross_show',array('id' => $cross->getId()));
                else
                    $url = $this->generateUrl('flycross_list');
                return $this->redirect($url);
            }
        }
        
        return array(
            'cross' => $cross,
            'form' => $form->createView());
    }

    /**
     * Edit cross
     * 
     * @Route("/crosses/edit/{id}", name="flycross_edit")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyCross")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $response = parent::baseEditAction($id, new FlyCrossType());
        
        if (isset($response['redirect'])) {
            $url = $this->generateUrl('flycross_show',array('id' => $response['entity']->getId()));
            return $this->redirect($url);
        } else {
            return array(
                'cross' => $response['entity'],
                'form' => $response['form']);
        }
    }

    /**
     * Delete cross (and its vial)
     * 
     * @Route("/crosses/delete/{id}", name="flycross_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyCross")
     * 
     * @param integer $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getEntityManager();
        $cross = $entityManager->find($this->getEntityClass(), $id);
        
        $entityManager->remove($cross);
        $entityManager->flush();
        
        //parent::baseDeleteAction($id);
        return $this->redirect($this->generateUrl('flycross_list'));
    }
    
    /**
     * Cascade ACL setting for stock vials
     * 
     * @param Object $entity
     * @param UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($cross, $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        parent::setACL($cross, $user, $mask);
        $vial = $cross->getVial();
        if ($vial) {
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
    public function handleBatchAction($action, $vials) {
        
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
    public function trashVials($crosses) {
        
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
    public function generateLabels($crosses) {
        
        $vials = new ArrayCollection();
        
        foreach ($crosses as $cross) {
            $vials->add($cross->getVial());
        }
        
        return parent::generateLabels($vials);
    }
}
