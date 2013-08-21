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

/**
 * InjectionVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class InjectionVialRepository extends SearchableVialRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder($options = array())
    {
        return parent::getListQueryBuilder($options)
            ->addSelect('s, sv, svs')
            ->leftJoin('e.targetStock', 's')
            ->leftJoin('e.targetStockVial', 'sv')
            ->leftJoin('sv.stock', 'svs');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchQueryBuilder($terms, $excluded = array(), $options = array())
    {
        return parent::getSearchQueryBuilder($terms, $excluded, $options)
            ->leftJoin('e.targetStock', 's')
            ->leftJoin('e.targetStockVial', 'sv')
            ->leftJoin('sv.stock', 'svs');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchResultCountQueryBuilder($terms, $excluded = array(), $options = array())
    {
        return parent::getSearchResultCountQueryBuilder($terms, $excluded, $options)
            ->leftJoin('e.targetStock', 's')
            ->leftJoin('e.targetStockVial', 'sv')
            ->leftJoin('sv.stock', 'svs');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchFields($options = array())
    {
        $fields = array(
            'e.constructName',
            's.name',
            's.genotype',
            'svs.name',
            'svs.genotype');
        if ((key_exists('notes', $options))&&($options['notes'])) {
            $fields[] = 'e.notes';
        }
        
        return $fields;
    }
    
    /**
     * {@inheritdoc}
     */
    public function search($terms, $excluded = array(), $options = array())
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));

        $expr = null;
        foreach ($terms as $term) {
            $expr = $qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->like('b.name', '\'%' . $term . '%\''),
                    $qb->expr()->like('b.genotype', '\'%' . $term . '%\'')
                ),
                $expr
            );
        }
        
        $query = $this->createQueryBuilder('b')
            ->where('b.setupDate > :date')
            ->andWhere('b.trashed = false')
            ->orderBy('b.setupDate', 'DESC')
            ->addOrderBy('b.id', 'DESC')
            ->setParameter('date', $date->format('Y-m-d'))
            ->andWhere('b.maleName like :term_1 or b.virginName like :term_2')
            ->setParameter('term_1', '%' . $term .'%')
            ->setParameter('term_2', '%' . $term .'%');

        return $query;
    }
    
    /**
     * Find similar injections
     *
     * @param InjectionVial $cross
     */
    public function findSimilar($injection)
    {
        $options = array();
        $options['filter'] = 'all';

        $startDate = clone $injection->getSetupDate();
        $stopDate = clone $injection->getSetupDate();
        $startDate->sub(new \DateInterval('P2W'));
        $stopDate->add(new \DateInterval('P2W'));

        $owner = $this->manager->getOwner($injection);

        $qb = $this->getListQueryBuilder($options);
        $qb->andWhere('e.injectionType = :injection_type')
           ->andWhere('e.constructName = :construct_name')
           ->andWhere('e.targetStock = :target_stock')
           ->andWhere('e.setupDate > :start_date')
           ->andWhere('e.setupDate <= :stop_date')
           ->orderBy('e.setupDate', 'ASC')
           ->addOrderBy('e.id', 'ASC')
           ->setParameter('injection_type', $injection->getInjectionType())
           ->setParameter('construct_name', $injection->getConstructName())
           ->setParameter('target_stock', $injection->getTargetStock())
           ->setParameter('start_date', $startDate->format('Y-m-d'))
           ->setParameter('stop_date', $stopDate->format('Y-m-d'));

        return $this->aclFilter->apply($qb, array('OWNER'), $owner)->getResult();
    }
}
