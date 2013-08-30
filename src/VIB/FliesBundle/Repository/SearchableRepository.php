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

use VIB\CoreBundle\Repository\EntityRepository;

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
    public function getSearchQuery($terms, $excluded = array(), $options = array())
    {
        $qb = $this->getSearchQueryBuilder($terms, $excluded, $options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : array();
        $user = isset($options['user']) ? $options['user'] : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->aclFilter->apply($qb, $permissions, $user);
    }
        
    /**
     * Get search QueryBuilder
     * 
     * @param array $terms
     * @param array $excluded
     * @param array $options
     * @return Doctrine\ORM\QueryBuilder Description
     */
    protected function getSearchQueryBuilder($terms, $excluded = array(), $options = array())
    {
        return $this->createQueryBuilder('e')
                ->add('where', $this->getSearchExpression($terms, $excluded, $options));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSearchResultCount($terms, $excluded = array(), $options = array())
    {
        return $this->getSearchResultCountQuery($terms, $excluded, $options)
                ->getSingleScalarResult();
    }
    
    /**
     * Get search result count Query
     * 
     * @param type $terms
     * @param type $excluded
     * @param type $options
     * @return 
     */
    protected function getSearchResultCountQuery($terms, $excluded = array(), $options = array())
    {
        $qb = $this->getSearchResultCountQueryBuilder($terms, $excluded, $options);
        $permissions = isset($options['permissions']) ? $options['permissions'] : array();
        $user = isset($options['user']) ? $options['user'] : null;
        
        return (false === $permissions) ? $qb->getQuery() : $this->aclFilter->apply($qb, $permissions, $user);
    }
    
    /**
     * Get search result count QueryBuilder
     * 
     * @param array $terms
     * @param array $excluded
     * @param array $options
     * @return Doctrine\ORM\QueryBuilder Description
     */
    protected function getSearchResultCountQueryBuilder($terms, $excluded = array(), $options = array())
    {
        return $this->createQueryBuilder('e')
                ->select('count(e.id)')
                ->add('where', $this->getSearchExpression($terms, $excluded, $options));
    }
    
    /**
     * Create DQL expression from search terms
     * 
     * @param array $terms
     * @param array $excluded
     * @param array $options
     * @return \Doctrine\ORM\Query\Expr
     */
    protected function getSearchExpression($terms, $excluded = array(), $options = array())
    {        
        $eb = $this->getEntityManager()->getExpressionBuilder();
        
        $expr = $eb->andX();
        foreach ($terms as $term) {
            $subexpr = $eb->orX();
            foreach ($this->getSearchFields($options) as $field) {
                $subexpr->add($eb->like($field, '\'%' . $term . '%\''));
            }
            $expr->add($subexpr);
        }
        foreach ($excluded as $term) {
            $subexpr = $eb->andX();
            foreach ($this->getSearchFields($options) as $field) {
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
    protected function getSearchFields($options = array())
    {
        $fields = array('e.id');
        
        return $fields;
    }
}
