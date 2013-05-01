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
 * FlyVialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder($options = array())
    {
        $builder = $this->createQueryBuilder('e')
                        ->orderBy('e.setupDate','DESC')
                        ->addOrderBy('e.id','DESC');
        
        return $this->applyQueryBuilderFilter($builder, $options);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getCountQueryBuilder($options = array())
    {
        $builder =  $this->createQueryBuilder('e')
                         ->select('count(e.id)');
        
        return $this->applyQueryBuilderFilter($builder, $options);
    }
    
    /**
     * 
     * @param \Doctrine\ORM\QueryBuilder $builder
     * @param array $options
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function applyQueryBuilderFilter($builder, $options = array())
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P2M'));
        $filter = isset($options['filter']) ? $options['filter'] : null;
        switch ($filter) {
            case 'all':
                break;
            case 'dead':
                $builder = $builder->where('e.setupDate <= :date')
                                   ->orWhere('e.trashed = true')
                                   ->setParameter('date', $date->format('Y-m-d'));
                break;
            case 'trashed':
                $builder = $builder->where('e.setupDate > :date')
                                   ->andWhere('e.trashed = true')
                                   ->setParameter('date', $date->format('Y-m-d'));
                break;
            default:
                $builder = $builder->where('e.setupDate > :date')
                                   ->andWhere('e.trashed = false')
                                   ->setParameter('date', $date->format('Y-m-d'));
                break;
        }
        
        return $builder;
    }
}
