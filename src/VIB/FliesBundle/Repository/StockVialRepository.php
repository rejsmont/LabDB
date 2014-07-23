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
use VIB\CoreBundle\Filter\SecureFilterInterface;
use VIB\FliesBundle\Entity\Stock;

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
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return parent::getListQueryBuilder($filter)
            ->addSelect('s')
            ->leftJoin('e.stock', 's');
    }
    
    /**
     *
     * @param  VIB\FliesBundle\Entity\Stock              $stock
     * @param  VIB\CoreBundle\Filter\ListFilterInterface $filter
     * @return Doctrine\Common\Collections\Collection
     */
    public function findLivingVialsByStock(Stock $stock, ListFilterInterface $filter = null)
    {
        $qb = $this->getListQueryBuilder($filter)
                   ->andWhere('e.stock = :stock')
                   ->setParameter('stock', $stock);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }

        return (false === $permissions) ? $qb->getQuery()->getResult() :
            $this->getAclFilter()->apply($qb, $permissions, $user)->getResult();
    }
    
    /**
     *
     * @param  VIB\FliesBundle\Entity\Stock              $stock
     * @param  VIB\CoreBundle\Filter\ListFilterInterface $filter
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsedVialCountByStock(Stock $stock, ListFilterInterface $filter = null)
    {
        $qb = $this->getCountQueryBuilder($filter)
                   ->andWhere('e.stock = :stock')
                   ->andWhere('e.id IN (SELECT sv.id FROM VIB\FliesBundle\Entity\StockVial sv '
                           . 'WHERE sv.children IS NOT EMPTY '
                           . 'OR sv.virginCrosses IS NOT EMPTY '
                           . 'OR sv.maleCrosses IS NOT EMPTY)')
                   ->setParameter('stock', $stock);
        
        if ($filter instanceof SecureFilterInterface) {
            $permissions = $filter->getPermissions();
            $user = $filter->getUser();
        } else {
            $permissions = array();
            $user = null;
        }

        return (false === $permissions) ? $qb->getQuery()->getSingleScalarResult() :
            $this->getAclFilter()->apply($qb, $permissions, $user)->getSingleScalarResult();
    }
}
