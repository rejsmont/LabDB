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

namespace VIB\FliesBundle\Repository;

use VIB\SearchBundle\Search\SearchQueryInterface;
use VIB\SearchBundle\Search\ACLSearchQueryInterface;
use VIB\FliesBundle\Search\SearchQuery;

/**
 * SearchableVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class SearchableVialRepository extends VialRepository implements SearchableRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQuery(SearchQueryInterface $search)
    {
        $qb = $this->getSearchQueryBuilder($search);
        $permissions = $search instanceof ACLSearchQueryInterface ? $search->getPermissions() : array();
        $user = $search instanceof ACLSearchQueryInterface ? $search->getUser() : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->aclFilter->apply($qb, $permissions, $user);
    }
        
    /**
     * Get search QueryBuilder
     * 
     * @param VIB\SearchBundle\Search\SearchQueryInterface $search
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getSearchQueryBuilder(SearchQueryInterface $search)
    {
        $qb = $this->createQueryBuilder('e')
                   ->add('where', $this->getSearchExpression($search));
        
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        
        if (!($search instanceof SearchQuery ? $search->searchDead() : false)) {
            $qb->andWhere('e.setupDate > :date')
               ->andWhere('e.trashed = false')
               ->setParameter('date', $date->format('Y-m-d'));
        }
        
        return $qb->orderBy('e.setupDate', 'DESC')
                  ->addOrderBy('e.id', 'DESC');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSearchResultCount(SearchQueryInterface $search)
    {
        return $this->getSearchResultCountQuery($search)
                    ->getSingleScalarResult();
    }
    
    /**
     * Get search result count Query
     * 
     * @param VIB\SearchBundle\Search\SearchQueryInterface $search
     * @return Doctrine\ORM\Query
     */
    protected function getSearchResultCountQuery(SearchQueryInterface $search)
    {
        $qb = $this->getSearchResultCountQueryBuilder($search);
        $permissions = $search instanceof ACLSearchQueryInterface ? $search->getPermissions() : array();
        $user = $search instanceof ACLSearchQueryInterface ? $search->getUser() : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->aclFilter->apply($qb, $permissions, $user);
    }
    
    /**
     * Get search result count QueryBuilder
     * 
     * @param VIB\SearchBundle\Search\SearchQueryInterface $search
     * @return Doctrine\ORM\QueryBuilder Description
     */
    protected function getSearchResultCountQueryBuilder(SearchQueryInterface $search)
    {
        $qb = $this->createQueryBuilder('e')
                   ->select('count(e.id)')
                    ->add('where', $this->getSearchExpression($search));
        
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
                
        if (!($search instanceof SearchQuery ? $search->searchDead() : false)) {
            $qb->andWhere('e.setupDate > :date')
               ->andWhere('e.trashed = false')
               ->setParameter('date', $date->format('Y-m-d'));
        }
        
        return $qb;
    }
    
    /**
     * Create DQL expression from search terms
     * 
     * @param VIB\SearchBundle\Search\SearchQueryInterface $search
     * @return \Doctrine\ORM\Query\Expr
     */
    protected function getSearchExpression(SearchQueryInterface $search)
    {        
        $eb = $this->getEntityManager()->getExpressionBuilder();
        
        $expr = $eb->andX();
        foreach ($search->getTerms() as $term) {
            $subexpr = $eb->orX();
            foreach ($this->getSearchFields($search) as $field) {
                $subexpr->add($eb->like($field, '\'%' . $term . '%\''));
            }
            $expr->add($subexpr);
        }
        foreach ($search->getExcluded() as $term) {
            $subexpr = $eb->andX();
            foreach ($this->getSearchFields($search) as $field) {
                $subexpr->add($eb->not($eb->like($field, '\'%' . $term . '%\'')));
            }
            $expr->add($subexpr);
        }
        
        return $expr;
    }
    
    /**
     * Get fields to search
     * 
     * @param type $options
     * @return array
     */
    protected function getSearchFields(SearchQueryInterface $search)
    {
        $fields = array('e.id');
        
        return $fields;
    }
}
