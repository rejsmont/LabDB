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

use Doctrine\ORM\EntityRepository;

/**
 * FlyVialRepository
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 */
class StockVialRepository extends EntityRepository
{
    /**
     * Return QueryBuilder object finding all living vials
     * 
     * @return Doctrine\ORM\QueryBuilder
     */
    public function findAllLivingQuery($qb = null) {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        
        if ($qb == null)
            $queryBuilder = $this->createQueryBuilder('b');
        else
            $queryBuilder = $qb;
        
        $query = $queryBuilder
            ->where('b.setupDate > :date')
            ->andWhere('b.trashed = false')
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('b.setupDate', 'DESC')
            ->addOrderBy('b.id', 'DESC');
                
        return $query;
    }
    
    /**
     * Return QueryBuilder object finding all vials
     * 
     * @return Doctrine\ORM\QueryBuilder
     */
    public function findAllQuery() {
        
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.setupDate', 'DESC')
            ->addOrderBy('b.id', 'DESC');
                
        return $query;
    }
        
    /**
     * Return living stock vials
     * 
     * @return mixed 
     */
    public function findAllLivingStocksQuery() {

        $query = $this->findAllLivingQuery()
            ->andWhere('b.stock is not null');
                
        return $query;
    }
    
    /**
     * Return living cross vials
     * 
     * @return mixed 
     */
    public function findAllLivingCrossesQuery() {

        $query = $this->findAllLivingQuery()
            ->andWhere('b.cross is not null');
                
        return $query;
    }
    
    /**
     * Return living cross vials
     * 
     * @return mixed 
     */
    public function findLivingStocksByName($term) {
        
        $query = $this->findAllLivingQuery()
            ->join('b.stock', 's')
            ->andWhere('s.name like :term')
            ->setParameter('term', '%' . $term .'%');
                
        return $query;
    }
    
}