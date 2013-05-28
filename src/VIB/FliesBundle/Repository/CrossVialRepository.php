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

namespace VIB\FliesBundle\Repository;

use VIB\CoreBundle\Doctrine\ObjectManager;

/**
 * FlyCrossRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialRepository extends VialRepository
{
    /**
     * Find similar crosses
     *
     * @param CrossVial $cross
     */
    public function findSimilar($cross)
    {
        $options = array();
        $options['filter'] = 'all';

        $startDate = clone $cross->getSetupDate();
        $stopDate = clone $cross->getSetupDate();
        $startDate->sub(new \DateInterval('P2W'));
        $stopDate->add(new \DateInterval('P2W'));

        $owner = $this->manager->getOwner($cross);

        $qb = $this->getListQueryBuilder($options);
        $qb->andWhere('e.maleName = :male_name')
           ->andWhere('e.virginName = :virgin_name')
           ->andWhere('e.setupDate > :start_date')
           ->andWhere('e.setupDate <= :stop_date')
           ->orderBy('e.setupDate', 'ASC')
           ->addOrderBy('e.id', 'ASC')
           ->setParameter('male_name', $cross->getUnformattedMaleName())
           ->setParameter('virgin_name', $cross->getUnformattedVirginName())
           ->setParameter('start_date', $startDate->format('Y-m-d'))
           ->setParameter('stop_date', $stopDate->format('Y-m-d'));

        return $this->aclFilter->apply($qb, array('OWNER'), $owner)->getResult();
    }

    /**
     * Search stocks
     *
     * @return mixed
     */
    public function search($term)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));

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
}
