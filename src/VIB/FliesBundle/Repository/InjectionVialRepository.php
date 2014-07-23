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

use VIB\CoreBundle\Filter\ListFilterInterface;
use VIB\SearchBundle\Search\SearchQueryInterface;
use VIB\FliesBundle\Filter\VialFilter;
use VIB\FliesBundle\Search\SearchQuery;
use VIB\FliesBundle\Entity\InjectionVial;


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
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return parent::getListQueryBuilder($filter)
            ->addSelect('ts, tsv, tsvs')
            ->leftJoin('e.targetStock', 'ts')
            ->leftJoin('e.targetStockVial', 'tsv')
            ->leftJoin('tsv.stock', 'tsvs');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchQueryBuilder(SearchQueryInterface $search)
    {
        return parent::getSearchQueryBuilder($search)
            ->leftJoin('e.targetStock', 'ts')
            ->leftJoin('e.targetStockVial', 'tsv')
            ->leftJoin('tsv.stock', 'tsvs');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchResultCountQueryBuilder(SearchQueryInterface $search)
    {
        return parent::getSearchResultCountQueryBuilder($search)
            ->leftJoin('e.targetStock', 'ts')
            ->leftJoin('e.targetStockVial', 'tsv')
            ->leftJoin('tsv.stock', 'tsvs');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchFields(SearchQueryInterface $search)
    {
        $fields = array(
            'e.constructName',
            'ts.name',
            'ts.genotype',
            'tsvs.name',
            'tsvs.genotype'
        );
        if ($search instanceof SearchQuery ? $search->searchNotes() : false) {
            $fields[] = 'e.notes';
        }
        
        return $fields;
    }
    
    /**
     * Find similar injections
     *
     * @param InjectionVial $injection
     */
    public function findSimilar(InjectionVial $injection)
    {
        $filter = new VialFilter();
        $filter->setAccess('shared');
        $filter->setFilter('all');

        $startDate = clone $injection->getSetupDate();
        $stopDate = clone $injection->getSetupDate();
        $startDate->sub(new \DateInterval('P2W'));
        $stopDate->add(new \DateInterval('P2W'));

        $owner = $this->getObjectManager()->getOwner($injection);

        $qb = $this->getListQueryBuilder($filter);
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

        return $this->getAclFilter()->apply($qb, $filter->getPermissions(), $owner)->getResult();
    }
}
