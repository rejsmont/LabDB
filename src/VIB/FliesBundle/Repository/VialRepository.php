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

use Doctrine\ORM\Query;
use VIB\CoreBundle\Doctrine\ObjectManager;
use VIB\CoreBundle\Filter\ListFilterInterface;
use VIB\CoreBundle\Filter\EntityFilterInterface;
use VIB\CoreBundle\Filter\SecureFilterInterface;
use VIB\CoreBundle\Repository\NewEntityRepository;
use VIB\FliesBundle\Filter\VialFilter;

/**
 * VialRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialRepository extends NewEntityRepository
{   
    /**
     * {@inheritdoc}
     */
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        $builder = $this->createQueryBuilder('e')
                        ->addSelect('o')
                        ->leftJoin('e.position', 'o')
                        ->orderBy('e.setupDate','DESC')
                        ->addOrderBy('e.id','DESC');

        return $this->applyQueryBuilderFilter($builder, $filter);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCountQueryBuilder(ListFilterInterface $filter = null)
    {
        $builder =  $this->createQueryBuilder('e')
                         ->select('count(e.id)');

        return $this->applyQueryBuilderFilter($builder, $filter);
    }

    /**
     *
     * @param  Doctrine\ORM\QueryBuilder                  $builder
     * @param  VIB\CoreBundle\Filter\ListFilterInterface  $filter
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyQueryBuilderFilter($builder, ListFilterInterface $filter)
    {
        $filterName = ($filter instanceof VialFilter) ? $filter->getFilter() : null;
        
        switch ($filterName) {
            case 'all':
                return $builder;
            case 'forgot':
                return $this->applyForgotFilter($builder);
            case 'dead':
                return $this->applyDeadFilter($builder);
            case 'trashed':
                return $this->applyTrashedFilter($builder);
            case 'due':
                return $this->applyDueFilter($builder);
            case 'overdue':
                return $this->applyOverDueFilter($builder);
            default:
                return $this->applyLivingFilter($builder);
        }
    }

    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyForgotFilter($builder)
    {
        return $builder->where('e.setupDate <= :twoMonthsAgo')
                       ->andWhere('e.trashed = false')
                       ->setParameter('twoMonthsAgo', $this->twoMonthsAgo()->format('Y-m-d'));
    }
    
    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyDeadFilter($builder)
    {
        return $builder->where('e.setupDate <= :twoMonthsAgo')
                       ->orWhere('e.trashed = true')
                       ->setParameter('twoMonthsAgo', $this->twoMonthsAgo()->format('Y-m-d'));
    }
    
    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyTrashedFilter($builder)
    {
        return $builder->where('e.setupDate > :twoMonthsAgo')
                       ->andWhere('e.trashed = true')
                       ->setParameter('twoMonthsAgo', $this->twoMonthsAgo()->format('Y-m-d'));
    }
    
    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyLivingFilter($builder)
    {
        return $builder->where('e.setupDate > :twoMonthsAgo')
                       ->andWhere('e.trashed = false')
                       ->setParameter('twoMonthsAgo', $this->twoMonthsAgo()->format('Y-m-d'));
    }
    
    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyDueFilter($builder)
    {
        return $this->applyLivingFilter($builder)
            ->andWhere('e.flipDate > :weekAgo')
            ->andWhere('e.flipDate < :inOneWeek')
            ->setParameter('weekAgo', $this->weekAgo()->format('Y-m-d'))
            ->setParameter('inOneWeek', $this->inOneWeek()->format('Y-m-d'));
    }

    /**
     * 
     * @param  Doctrine\ORM\QueryBuilder  $builder
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyOverDueFilter($builder)
    {
        return $this->applyLivingFilter($builder)
            ->andWhere('e.flipDate <= :weekAgo')
            ->andWhere('e.virginCrosses is EMPTY')
            ->andWhere('e.maleCrosses is EMPTY')
            ->andWhere('e.id NOT IN (SELECT sv.id FROM VIB\FliesBundle\Entity\StockVial sv WHERE sv.children IS NOT EMPTY)')
            ->andWhere('e.id NOT IN (SELECT cv.id FROM VIB\FliesBundle\Entity\CrossVial cv WHERE cv.children IS NOT EMPTY)')
            ->andWhere('e.id NOT IN (SELECT iv.id FROM VIB\FliesBundle\Entity\InjectionVial iv WHERE iv.children IS NOT EMPTY)')
            ->setParameter('weekAgo', $this->weekAgo()->format('Y-m-d'));
    }
    
    /**
     * Return date two months ago
     * 
     * @return \DateTime
     */
    protected function twoMonthsAgo()
    {
        $twoMonthsAgo = new \DateTime();
        $twoMonthsAgo->sub(new \DateInterval('P2M'));
        
        return $twoMonthsAgo;
    }
    
    /**
     * Return date one week ago
     * 
     * @return \DateTime
     */
    protected function weekAgo()
    {
        $weekAgo = new \DateTime();
        $weekAgo->sub(new \DateInterval('P1W'));
        
        return $weekAgo;
    }
    
    /**
     * Return date in one week
     * 
     * @return \DateTime
     */
    protected function inOneWeek()
    {
        $inOneWeek = new \DateTime();
        $inOneWeek->add(new \DateInterval('P1W'));
        
        return $inOneWeek;
    }
    
    /**
     * Return dates when $user should flip vials
     *
     * @param  Symfony\Component\Security\Core\User\UserInterface $user
     * @return array
     */
    public function getFlipDates($user)
    {
        $qb = $this->getListQueryBuilder();
        $qb->groupBy('e.setupDate')
           ->addGroupBy('e.incubator')
           ->addGroupBy('e.flipDate')
           ->orderBy('e.setupDate', 'DESC');

        $vials = $this->getAclFilter()->apply($qb, array('OWNER'), $user)->getResult();
        $dates = array();
        foreach ($vials as $vial) {
            $dates[] = $vial->getFlipDate();
        }

        return array_unique($dates, SORT_REGULAR);
    }
}
