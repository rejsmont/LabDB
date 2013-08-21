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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VIB\CoreBundle\Controller\AbstractController;
use VIB\FliesBundle\Repository\SearchableRepositoryInterface;

use VIB\FliesBundle\Form\SearchType;
use VIB\FliesBundle\Form\AdvancedSearchType;

/**
 * Description of SearchController
 *
 * @Route("/search")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends AbstractController
{
    /**
     * Handle search request
     *
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function searchAction()
    {
        return $this->render('VIBFliesBundle:Search:search.html.twig');
    }
    
    /**
     * Handle advanced search request
     *
     * @Template()
     * @Route("/") 
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function advancedAction()
    {
        $form = $this->createForm(new AdvancedSearchType());
        return array('form' => $form->createView());
    }

    /**
     * Render search form
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function formAction()
    {
        $form = $this->createForm(new SearchType());
        $msie_version = 10;
        $request = $this->get('request');
        $user_agent = $request->headers->get('User-Agent');
        $is_msie = strpos($user_agent, 'MSIE') !== false;
        if ($is_msie) {
            $matches = array();
            preg_match('/MSIE (.*?)\./', $user_agent, $matches);
            $msie_version = array_key_exists(1, $matches) ? $matches[1] : 0;
            if ($msie_version < 8) {
                $msie = true;
            } else {
                $msie = false;
            }
        } else {
            $msie = false;
        }

        return $this->render('VIBFliesBundle:Search:form.html.twig', array(
            'form' => $form->createView(),
            'msie' => $msie ));
    }

    /**
     * Handle search result
     *
     * @Route("/result/")
     * @Template()
     *
     * @return array
     */
    public function resultAction()
    {
        $om = $this->getObjectManager();
        $form = $this->createForm(new SearchType());
        $advForm = $this->createForm(new AdvancedSearchType());
        $request = $this->get('request');
        $session = $request->getSession();
        $securityContext = $this->getSecurityContext();

        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            $advForm->bind($request);
            
            if ($form->isValid()) {
                $data = $form->getData();
                $advanced = false;
            } elseif ($advForm->isValid()) {
                $data = $advForm->getData();
                $advanced = true;
            } else {
                $data = false;
            }
            
            if (false !== $data) {
                $term = $data['query'];
                if ('' == $data['filter']) {
                    if (preg_match("/^R\d+$/",$term) === 1) {
                        $filter = 'rack';
                        $id = (integer) str_replace('R','',$term);
                    } elseif (is_numeric($term)) {
                        $filter = 'vial';
                        $id = (integer) $term;
                    } else {
                        $filter = 'stock';
                    }
                } else {
                    $filter = $data['filter'];
                }
                $exclude = $advanced ? $data['exclude'] : '';
                $opts = $advanced ? $data['options'] : array();
                $session->set('search_query',$term);
                $session->set('search_exclude',$exclude);
                $session->set('search_filter',$filter);
                $session->set('search_options',$opts);
            }
        } else {
            $term = $session->get('search_query');
            $exclude = $session->get('search_exclude');
            $filter = $session->get('search_filter');
            $opts = $session->get('search_options');
        }
        
        $repository = $om->getRepository($this->filterToClass($filter));
        
        if ($repository instanceof SearchableRepositoryInterface) {
            
            $terms = $term != '' ? explode(' ',$term) : array();
            $excluded = $exclude != '' ? explode(' ',$exclude) : array();
            $options = array();
            $options['search'] = $opts;
            $options['user'] = $this->getUser();
            $options['permissions'] = in_array('private', $opts) ? array('OWNER') : 
                ($securityContext->isGranted('ROLE_ADMIN') ? false : array('VIEW'));
            $options['dead'] = in_array('dead', $opts);
            $options['notes'] = in_array('notes', $opts);
            
            $resultCount = $repository->getSearchResultCount($terms, $excluded, $options);
            $result = $repository->getSearchQuery($terms, $excluded, $options)
                                 ->setHint('knp_paginator.count', $resultCount);
            $paginator  = $this->getPaginator();
            $page = $this->getCurrentPage();
            $entities = $paginator->paginate($result, $page, 25);

            return array('entities' => $entities,
                         'query' => $term,
                         'exclude' => $exclude,
                         'filter' => $filter,
                         'options' => $options);
        }

        $url = $this->generateUrl('vib_flies_' . $filter . '_show', array('id' => $id));
        return $this->redirect($url);
    }
    
    /**
     * Convert filter string to class name
     * 
     * @param string $filter
     * @return string
     */
    private function filterToClass($filter)
    {
        $lookup = array(
            'vial' => 'VIB\FliesBundle\Entity\Vial',
            'rack' => 'VIB\FliesBundle\Entity\Rack',
            'stock' => 'VIB\FliesBundle\Entity\Stock',
            'crossvial' => 'VIB\FliesBundle\Entity\CrossVial',
            'injectionvial' => 'VIB\FliesBundle\Entity\InjectionVial',
        );
        
        return key_exists($filter, $lookup) ? $lookup[$filter] : '';
    }
}
