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
 * StockRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function getListQuery($options = array())
    {
        $query = parent::getListQuery($options);
        if ($options['filter'] == '') {
            $qb = $this->getListQueryBuilder($options);
            $permissions = array('OWNER');
            $user = isset($options['user']) ? $options['user'] : null;

            return $this->aclFilter->apply($qb, $permissions, $user, 'v');
        } else {
            return $query;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder($options = array())
    {
        $qb = $this->createQueryBuilder('e')
                   ->orderBy('e.name');
        if ($options['filter'] == '') {
            $date = new \DateTime();
            $date->sub(new \DateInterval('P2M'));

            return $qb->distinct()
                      ->join('e.vials','v')
                      ->andWhere('v.setupDate > :date')
                      ->andWhere('v.trashed = false')
                      ->setParameter('date', $date->format('Y-m-d'));
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQuery($options = array())
    {
        $query = parent::getCountQuery($options);
        if ($options['filter'] == '') {
            $qb = $this->getCountQueryBuilder($options);
            $permissions = array('OWNER');
            $user = isset($options['user']) ? $options['user'] : null;

            return $this->aclFilter->apply($qb, $permissions, $user, 'v');
        } else {
            return $query;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getCountQueryBuilder($options = array())
    {
        $qb = $this->createQueryBuilder('e')
                   ->select('count(DISTINCT e.id)');
        if ($options['filter'] == '') {
            $date = new \DateTime();
            $date->sub(new \DateInterval('P2M'));

            return $qb->join('e.vials','v')
                      ->andWhere('v.setupDate > :date')
                      ->andWhere('v.trashed = false')
                      ->setParameter('date', $date->format('Y-m-d'));
        }

        return $qb;
    }

    /**
     * Return stocks
     *
     * @return mixed
     */
    public function findStocksByName($term)
    {
        $query = $this->createQueryBuilder('b')
                      ->andWhere('b.name like :term')
                      ->setParameter('term', '%' . $term .'%');

        return $query;
    }

    /**
     * Search stocks
     *
     * @return mixed
     */
    public function search($term)
    {
        $terms = explode(" ", $term);
        $qb = $this->createQueryBuilder('b');
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
        $qb->add('where', $expr);

        return $qb;
    }
}
