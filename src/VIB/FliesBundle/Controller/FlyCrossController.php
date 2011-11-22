<?php

namespace VIB\FliesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

use VIB\FliesBundle\Entity\FlyCross;
use VIB\FliesBundle\Entity\ListCollection;
use VIB\FliesBundle\Form\FlyCrossType;
use VIB\FliesBundle\Form\FlyCrossSelectType;

class FlyCrossController extends Controller
{
    /**
     * List crosses
     * 
     * @Route("/crosses/", name="flycross_list")
     * @Route("/crosses/page/{page}", name="flycross_listpage")
     * @Template()
     */
    public function listAction($page = 1)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $query = $em->getRepository('VIBFliesBundle:FlyCross')->findAllLivingQuery();
        
        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(15);
        $pager->setCurrentPage($page);
        $crosses = $pager->getCurrentPageResults();
        
        $list = new ListCollection($crosses);
        $form = $this->createForm(new FlyCrossSelectType(), $list);

        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {

            }
        }
                
        
        return array('form' => $form->createView(),
                     'list' => $list,
                     'pager' => $pager);
    }
    
    /**
     * Show cross
     * 
     * @Route("/crosses/show/{id}", name="flycross_show")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyCross")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('VIBFliesBundle:FlyCross', $id);
        
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
        
        $form = $this->get('form.factory')
                ->create(new FlyCrossType(), $cross);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($cross);
                $em->persist($cross->getVial());
                $em->flush();
                
                $securityContext = $this->get('security.context');
                $user = $securityContext->getToken()->getUser();
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                
                $aclProvider = $this->get('security.acl.provider');
                
                $objectIdentity = ObjectIdentity::fromDomainObject($cross);
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
                
                $objectIdentity = ObjectIdentity::fromDomainObject($cross->getVial());
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
                
                return $this->redirect($this->generateUrl('flycross_show',array('id' => $cross->getId())));
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
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('VIBFliesBundle:FlyCross', $id);
        
        $form = $this->get('form.factory')
                ->create(new FlyCrossType(), $cross);
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $em->persist($cross);
                $em->persist($cross->getVial());
                $em->flush();
                return $this->redirect($this->generateUrl('flycross_show',array('id' => $cross->getId())));
            }
        }
        
        return array('cross' => $cross,
                     'form' => $form->createView());
    }

    /**
     * Delete cross (and its vial)
     * 
     * @Route("/crosses/delete/{id}", name="flycross_delete")
     * @Template()
     * @ParamConverter("id", class="VIBFliesBundle:FlyCross")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $cross = $em->find('VIBFliesBundle:FlyCross', $id);

        $em->remove($cross->getVial());
        $em->flush();       
        $em->remove($cross);
        $em->flush();
        return $this->redirect($this->generateUrl('flycross_list'));
    }
}
