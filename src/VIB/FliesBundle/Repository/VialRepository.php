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

use Doctrine\ORM\EntityRepository;

/**
 * FlyVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialRepository extends EntityRepository
{
    /**
     * Return QueryBuilder object finding all living vials
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function findAllLivingQuery()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));

        $query = $this->createQueryBuilder('b')
            ->where('b.setupDate > :date')
            ->andWhere('b.trashed = false')
            ->orderBy('b.setupDate', 'DESC')
            ->addOrderBy('b.id', 'DESC')
            ->setParameter('date', $date->format('Y-m-d'));

        return $query;
    }

    /**
     * Return QueryBuilder object finding all vials
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function findAllQuery()
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.setupDate', 'DESC')
            ->addOrderBy('b.id', 'DESC');

        return $query;
    }
}
