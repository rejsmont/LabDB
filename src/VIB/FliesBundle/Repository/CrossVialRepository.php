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
 * FlyCrossRepository
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 */
class CrossVialRepository extends EntityRepository
{
    /**
     * Search stocks
     * 
     * @return mixed 
     */
    public function search($term) {
        
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
