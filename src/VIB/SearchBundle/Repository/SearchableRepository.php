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

namespace VIB\SearchBundle\Repository;

use VIB\CoreBundle\Repository\EntityRepository;
use VIB\SearchBundle\Search\SearchQueryInterface;
use VIB\SearchBundle\Search\ACLSearchQueryInterface;

/**
 * SearchableRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class SearchableRepository extends EntityRepository implements SearchableRepositoryInterface
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
        $qb = $this->createQueryBuilder('e');
        $expr = $this->getSearchExpression($search);
        return (null !== $expr) ? $qb->add('where', $expr) : $qb;
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
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function getSearchResultCountQueryBuilder(SearchQueryInterface $search)
    {
        $qb = $this->createQueryBuilder('e')->select('count(e.id)');
        $expr = $this->getSearchExpression($search);
        return (null !== $expr) ? $qb->add('where', $expr) : $qb;
    }
    
    /**
     * Create DQL expression from search terms
     * 
     * @param VIB\SearchBundle\Search\SearchQueryInterface $search
     * @return \Doctrine\ORM\Query\Expr
     */
    protected function getSearchExpression(SearchQueryInterface $search)
    {        
        if ((count($search->getTerms()) + count($search->getExcluded())) < 1) {
            return null;
        }

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
