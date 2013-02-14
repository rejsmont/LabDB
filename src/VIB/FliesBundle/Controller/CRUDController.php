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

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Base class for CRUD operations CRUDController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CRUDController extends AbstractController {
    
    /**
     * Entity class for this controller
     * 
     * @var string
     */
    protected $entityClass;
    
    /**
     * Entity name for this controller
     * 
     * @var string
     */
    protected $entityName;
    
    /**
     * Construct CRUDController
     *
     */ 
    public function __construct()
    {
        $this->entityClass = null;
    }
        
    /**
     * List action
     * 
     * @param integer $page
     * @param Doctrine\ORM\QueryBuilder $query
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function baseListAction($page = 1, $queryBuilder = null, $maxPerPage = 15)
    {        
        $em = $this->getDoctrine()->getManager();
        
        if ($queryBuilder == null) {
            $queryBuilder = $em->getRepository($this->getEntityClass())->createQueryBuilder('q');
        }
        
        $entities = $queryBuilder->getQuery()->getResult();
        
        return array('entities' => $entities);
    }
    
    /**
     * Show action
     * 
     * @param Object $entity
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function baseShowAction($entity)
    {
        return array($this->getEntityName() => $entity);
    }
    
    /**
     * Create action
     * 
     * @param Object $entity
     * @param AbstractType $entityType
     * @param string $route
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function baseCreateAction($entity, AbstractType $entityType, $route)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($entityType, $entity);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $em->persist($entity);
                $em->flush();

                $this->setACL($entity);
                
                $url = $this->generateUrl($route,array('id' => $entity->getRoutableId()));
                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Edit action
     *     
     * @param Object $entity
     * @param AbstractType $entityType
     * @param string $route
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function baseEditAction($entity, AbstractType $entityType, $route)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($entityType, $entity);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $em->persist($entity);
                $em->flush();
                
                $url = $this->generateUrl($route,array('id' => $entity->getRoutableId()));
                return $this->redirect($url);
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Delete action
     * 
     * @param Object $entity
     * @param string $route
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function baseDeleteAction($entity, $route)
    {
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl($route));
    }

    /**
     * Set ACL for entity
     * 
     * @param Object $entity
     * @param UserInterface|null $user
     * @param integer $mask
     */
    protected function setACL($entity, UserInterface $user = null, $mask = MaskBuilder::MASK_OWNER) {
        
        if ($user === null) {
            $user = $this->getUser();
        }
        
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $aclProvider = $this->getAclProvider();
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($securityIdentity, $mask);
        $aclProvider->updateAcl($acl);
    }
    
    public function getEntityClass() {
        return $this->entityClass;
    }
    
    public function getEntityName() {
        return $this->entityName;
    }


}
?>
