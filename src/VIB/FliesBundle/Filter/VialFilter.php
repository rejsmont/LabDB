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

namespace VIB\FliesBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use VIB\CoreBundle\Filter\SecureListFilter;
use VIB\CoreBundle\Filter\SortFilterInterface;
use VIB\CoreBundle\Filter\RedirectFilterInterface;

/**
 * Description of VialFilter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialFilter extends SecureListFilter implements SortFilterInterface, RedirectFilterInterface {
    
    protected $health;
    
    protected $living;
    
    protected $dead;
    
    protected $sort;
    
    protected $redirect;
    
    /**
     * Construct VialFilter
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function __construct(Request $request = null, SecurityContextInterface $securityContext = null)
    {
        parent::__construct($request, $securityContext);

        if (null !== $request) {
            if ($request->get('resolver', 'off') == 'on') {
                $this->setHealth($request->get('health', 'living'));
                $this->setLiving($request->get('living', 'all'));
                $this->setDead($request->get('dead', 'all'));
                $this->redirect = true;
            } else {
                $this->setFilter($request->get('filter', 'living'));
                $this->redirect = false;
            }
        } else {
            $this->setFilter('living');
            $this->redirect = false;
        }
        
        $this->sort = 'setup';
    }

    /**
     * 
     * @return string
     */
    public function getHealth() {
        return $this->health;
    }

    /**
     * 
     * @param string $health
     */
    public function setHealth($health) {
        $this->health = $health;
    }
    
    /**
     * 
     * @return string
     */
    public function getLiving() {
        return $this->living;
    }

    /**
     * 
     * @param string $living
     */
    public function setLiving($living) {
        $this->living = $living;
    }
    
    /**
     * 
     * @return string
     */
    public function getDead() {
        return $this->dead;
    }

    /**
     * 
     * @param string $dead
     */
    public function setDead($dead) {
        $this->dead = $dead;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort) {
        $this->sort = $sort;
    }
    
    /**
     * Get filter
     * 
     * @return string
     */
    public function getFilter() {
        if ($this->health == 'living') {
            if ($this->living == 'due') {
                return 'due';
            } elseif ($this->living == 'overdue') {
                return 'overdue';
            } else {
                return 'living';
            }
        } elseif ($this->health == 'dead') {
            if ($this->dead == 'trashed') {
                return 'trashed';
            } elseif ($this->dead == 'forgot') {
                return 'forgot';
            } else {
                return 'dead';
            }
        } else {
            return 'all';
        }
    }

    /**
     * Set filter
     * 
     * @param string $filter
     */
    public function setFilter($filter) {
        switch ($filter) {
            case 'all':
                $this->health = 'all';
                break;
            case 'dead':
                $this->health = 'dead';
                $this->dead = 'all';
                $this->living = 'all';
                break;
            case 'forgot':
                $this->health = 'dead';
                $this->dead = 'forgot';
                $this->living = 'all';
                break;
            case 'trashed':
                $this->health = 'dead';
                $this->dead = 'trashed';
                $this->living = 'all';
                break;
            case 'due':
                $this->health = 'living';
                $this->living = 'due';
                $this->dead = 'all';
                break;
            case 'overdue':
                $this->health = 'living';
                $this->living = 'overdue';
                $this->dead = 'all';
                break;
            case 'living':
            default:
                $this->health = 'living';
                $this->living = 'all';
                $this->dead = 'all';
                break;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function needRedirect()
    {
        return $this->redirect;
    }
}
