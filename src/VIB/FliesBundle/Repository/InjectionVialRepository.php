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
class InjectionVialRepository extends VialRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder($options = array())
    {
        return parent::getListQueryBuilder($options)
            ->addSelect('s, sv')
            ->leftJoin('e.targetStock', 's')
            ->leftJoin('e.targetStockVial', 'sv');
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
