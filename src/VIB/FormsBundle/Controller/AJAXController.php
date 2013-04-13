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

namespace VIB\FormsBundle\Controller;

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
     * @Route("/_ajax/choices/{class}/{property}", name="VIBFormsBundle_ajax_choices")
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string                                     $class    Entity class to search for
     * @param  string                                     $property Property to lookup in search
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function choicesAction(Request $request, $class, $property)
    {
        $query = $request->query->get('query');

        $qb = $this->getDoctrine()
                   ->getRepository($class)
                   ->createQueryBuilder('b');

        $terms = explode(" ",$query);
        foreach ($terms as $term) {
          $qb = $qb->andWhere("b." . $property . " like '%" . $term . "%'");
        }
        $found = $qb->getQuery()->getResult();

        $stockNames = array();
        foreach ($found as $stock) {
            $stockNames[] = $stock->getName();
        }

        $response = new JsonResponse();
        $response->setData(array('options' => $stockNames));

        return $response;
    }
}
