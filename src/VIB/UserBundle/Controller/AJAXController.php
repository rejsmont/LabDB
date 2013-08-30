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
     * @Route("/_ajax/choices/users", name="vib_user_ajax_choices")
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function nameChoicesAction(Request $request)
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
            $options[] = $user->getFullName() . "[[" . $user->getUsername() . "]]";
        }

        $response = new JsonResponse();
        $response->setData(array('options' => $options));

        return $response;
    }
}
