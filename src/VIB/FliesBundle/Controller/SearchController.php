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

use VIB\SearchBundle\Controller\SearchController as BaseSearchController;

/**
 * Search controller for the flies bundle
 *
 * @Route("/search")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends BaseSearchController
{
    
    protected function getClassLookupTable() {
        return array(
            'vial' => 'VIB\FliesBundle\Entity\Vial',
            'rack' => 'VIB\FliesBundle\Entity\Rack',
            'stock' => 'VIB\FliesBundle\Entity\Stock',
            'crossvial' => 'VIB\FliesBundle\Entity\CrossVial',
            'injectionvial' => 'VIB\FliesBundle\Entity\InjectionVial',
        );
    }

    protected function getDefaultFilter($term = '') {
        if (preg_match("/^R\d+$/",$term) === 1) {
            return 'rack';
        } elseif (is_numeric($term)) {
            $filter = 'vial';
        } else {
            $filter = 'stock';
        }
    }

    protected function handleNonSearchableRepository($repository, $term, $filter, $exclude = '', $options = array()) {
        switch ($filter) {
            case 'rack':
                $id = (integer) str_replace('R','',$term);
                break;
            case 'vial':
                $id = (integer) $term;
                break;
            default:
                $id = false;
        }
        
        if ((false !== $id)&&($id > 0)) {
            $url = $this->generateUrl($this->getSearchRealm() . "_" . $filter . '_show', array('id' => $id));
            return $this->redirect($url);
        } else {
            return $this->createNotFoundException();
        }
    }
    
    protected function getSearchRealm() {
        return 'vib_flies';
    }    
}
