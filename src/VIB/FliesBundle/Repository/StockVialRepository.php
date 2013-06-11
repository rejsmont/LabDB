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
 * StockVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockVialRepository extends VialRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder($options = array())
    {
        return parent::getListQueryBuilder($options)
            ->addSelect('s')
            ->leftJoin('e.stock', 's');
    }
    
    /**
     *
     * @param  type                                   $stock
     * @return Doctrine\Common\Collections\Collection
     */
    public function findLivingVialsByStock($stock, $options = array())
    {
        $qb = $this->getListQueryBuilder($options)
                   ->andWhere('e.stock = :stock')
                   ->setParameter('stock', $stock);
        $permissions = isset($options['permissions']) ? $options['permissions'] : array();
        $user = isset($options['user']) ? $options['user'] : null;
        if (false === $permissions) {
            return $qb->getQuery()->useResultCache(true)->getResult();
        } else {
            return $this->aclFilter->apply($qb, $permissions, $user)->getResult();
        }
    }
}
