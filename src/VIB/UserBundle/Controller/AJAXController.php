<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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

namespace VIB\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * This controller generates choices list for EntityTypeaheadType
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends Controller
{
    /**
     * Search for the specified entity by its property
     *
     * @Route("/choices/users", name="vib_user_ajax_choices")
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userChoicesAction(Request $request)
    {
        $query = $request->query->get('query');

        $qb = $this->getDoctrine()
                   ->getManager()
                   ->getRepository('VIB\UserBundle\Entity\User')
                   ->createQueryBuilder('u');
        $eb = $this->getDoctrine()
                   ->getManager()
                   ->getExpressionBuilder();
        
        $terms = explode(" ",$query);
        if (count($terms) > 0)
        {
            $expr = $eb->andX();
            foreach ($terms as $term) {
                $subexpr = $eb->orX();
                foreach (array('u.surname','u.givenName', 'u.username') as $field) {
                    $subexpr->add($eb->like($field, '\'%' . $term . '%\''));
                }
                $expr->add($subexpr);
            }
            $qb->add('where', $expr);
        }
        $found = $qb->getQuery()->getResult();

        $options = array();
        foreach ($found as $user) {
            $options[] = array('fullname' => $user->getFullName(), 'username' => $user->getUsername());
        }

        $response = new JsonResponse();
        $response->setData($options);

        return $response;
    }
    
    /**
     * Search for the specified entity by its property
     *
     * @Route("/choices/roles", name="vib_role_ajax_choices")
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function roleChoicesAction(Request $request)
    {
        $query = $request->query->get('query');
        $query_string = 'SELECT identifier FROM acl_security_identities WHERE identifier like \'ROLE_%\'';
        
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('identifier', 'role');
        
        $sql_query = $this->getDoctrine()->getManager()->createNativeQuery($query_string, $rsm);
        
        $roles = array();
        
        $found = $sql_query->getResult();
        foreach ($found as $result) {
            $roles[] = $result['role'];
        }
        
        $hierarchy = $this->container->getParameter('security.role_hierarchy.roles');
        foreach ($hierarchy as $parent => $children) {
            $roles[] = $parent;
            foreach($children as $child) {
                $roles[] = $child;
            }
        }
        
        if (($user = $this->getUser()) instanceof UserInterface) {
            foreach ($user->getRoles() as $role) {
                $roles[] = $role;
            }
        }
        
        $roles = array_unique($roles);
        sort($roles);
        
        $options = array();
        foreach($roles as $role) {
            $role = ucfirst(strtolower(str_replace(array("ROLE_","_"), array(""," "), $role)));
            if (stristr($role ,$query)) {
                $options[] = $role;
            }
        }
        
        $response = new JsonResponse();
        $response->setData($options);

        return $response;
    }
}
