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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\ORM\QueryBuilder;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Base class for CRUD operations CRUDController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CRUDController extends Controller {
    
    protected $entityClass;
    
    private $entityManager;
    private $formFactory;
    private $request;
    private $aclProvider;
    private $securityContext;
    private $currentUser;
    
    /**
     * Construct CRUDController
     *
     */ 
    public function __construct()
    {
        $this->entityClass = null;
        $this->entityManager = null;
        $this->formFactory = null;
        $this->request = null;
        $this->aclProvider = null;
        $this->securityContext = null;
        $this->currentUser = null;
    }
        
    /**
     * List action
     * 
     * @param integer $page
     * @param Doctrine\ORM\QueryBuilder $query
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function baseListAction($page = 1, $queryBuilder = null, $maxPerPage = 15)
    {        
        $entityManager = $this->getEntityManager();
        
        if ($queryBuilder == null) {
            $queryBuilder = $entityManager->getRepository($this->getEntityClass())->createQueryBuilder('q');
        }
        
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($page);
        $entities = $pager->getCurrentPageResults();
        
        return array('entities' => $entities,
                     'pager' => $pager);
    }
    
    /**
     * Show action
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function baseShowAction($id)
    {
        $entity = $this->getEntityManager()->find($this->getEntityClass(), $id);
        
        return array('entity' => $entity);
    }
    
    
    /**
     * Create action
     * 
     * @param Object $entity
     * @param AbstractType $entityType
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function baseCreateAction($entity, AbstractType $entityType)
    {
        $entityManager = $this->getEntityManager();
        $form = $this->getFormFactory()->create($entityType, $entity);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $entityManager->persist($entity);
                $entityManager->flush();

                $this->setACL($entity);
                
                return array(
                    'entity' => $entity,
                    'redirect' => true);
            }
        }
        
        return array(
            'entity' => $entity,
            'form' => $form->createView());
    }

    /**
     * Edit action
     *     
     * @param mixed $id
     * @param AbstractType $entityType
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function baseEditAction($id, AbstractType $entityType)
    {
        $entityManager = $this->getEntityManager();
        $entity = $entityManager->find($this->getEntityClass(), $id);
        $form = $this->getFormFactory()->create($entityType, $entity);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $entityManager->persist($entity);
                $entityManager->flush();
                
                return array(
                    'entity' => $entity,
                    'redirect' => true);
            }
        }
        
        return array(
            'entity' => $entity,
            'form' => $form->createView());
    }

    /**
     * Delete action
     * 
     * @param mixed $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function baseDeleteAction($id)
    {
        $entityManager = $this->getEntityManager();
        $entity = $entityManager->find($this->getEntityClass(), $id);
        
        $entityManager->remove($entity);
        $entityManager->flush();
        
        return array('redirect' => true);
    }

    /**
     * Set ACL for entity
     * 
     * @param Object $entity
     * @param UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($entity, $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        if ($user === null) {
            $user = $this->getCurrectUser();
        }
        
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $aclProvider = $this->getAclProvider();
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($securityIdentity, $mask);
        $aclProvider->updateAcl($acl);
    }
    
    /**
     * Get current user
     * 
     * @return UserInterface 
     */
    protected function getCurrectUser() {
        if ($this->currentUser == null)
            $this->currentUser = $this->getSecurityContext()->getToken()->getUser();
        return $this->currentUser;
    }
    
    public function getEntityClass() {
        return $this->entityClass;
    }

    public function getEntityManager() {
        if ($this->entityManager == null)
            $this->entityManager = $this->get('doctrine.orm.entity_manager');
        return $this->entityManager;
    }

    public function getFormFactory() {
        if ($this->formFactory == null)
            $this->formFactory = $this->get('form.factory');
        return $this->formFactory;
    }

    public function getRequest() {
        if ($this->request == null)
            $this->request = $this->get('request');
        return $this->request;
    }
    
    public function getAclProvider() {
        if ($this->aclProvider == null)
            $this->aclProvider = $this->get('security.acl.provider');
        return $this->aclProvider;
    }

    public function getSecurityContext() {
        if ($this->securityContext == null)
            $this->securityContext = $this->get('security.context');
        return $this->securityContext;
    }


}
?>
