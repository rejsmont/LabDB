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
use VIB\CoreBundle\Repository\EntityRepository;


/**
 * VialRepository
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
                        ->addSelect('o')
                        ->leftJoin('e.position', 'o')
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
     * @param  Doctrine\ORM\QueryBuilder $builder
     * @param  array                     $options
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function applyQueryBuilderFilter($builder, $options = array())
    {
        $twoMonthsAgo = new \DateTime();
        $twoMonthsAgo->sub(new \DateInterval('P2M'));
        $weekAgo = new \DateTime();
        $weekAgo->sub(new \DateInterval('P1W'));
        $inOneWeek = new \DateTime();
        $inOneWeek->add(new \DateInterval('P1W'));
        $filter = isset($options['filter']) ? $options['filter'] : null;
        switch ($filter) {
            case 'all':
                break;
            case 'overdue':
                $builder = $builder->where('e.flipDate <= :weekAgo')
                                   ->andWhere('e.setupDate > :twoMonthsAgo')
                                   ->andWhere('e.trashed = false')
                                   ->setParameter('weekAgo', $weekAgo->format('Y-m-d'))
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
            case 'due':
                $builder = $builder->where('e.flipDate > :weekAgo')
                                   ->andWhere('e.flipDate < :inOneWeek')
                                   ->andWhere('e.setupDate > :twoMonthsAgo')
                                   ->andWhere('e.trashed = false')
                                   ->setParameter('weekAgo', $weekAgo->format('Y-m-d'))
                                   ->setParameter('inOneWeek', $inOneWeek->format('Y-m-d'))
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
            case 'forgot':
                $builder = $builder->where('e.setupDate <= :twoMonthsAgo')
                                   ->andWhere('e.trashed = false')
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
            case 'dead':
                $builder = $builder->where('e.setupDate <= :twoMonthsAgo')
                                   ->orWhere('e.trashed = true')
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
            case 'trashed':
                $builder = $builder->where('e.setupDate > :twoMonthsAgo')
                                   ->andWhere('e.trashed = true')
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
            default:
                $builder = $builder->where('e.setupDate > :twoMonthsAgo')
                                   ->andWhere('e.trashed = false')
                                   ->setParameter('twoMonthsAgo', $twoMonthsAgo->format('Y-m-d'));
                break;
        }

        return $builder;
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
            $dates[] = $vial->getRealFlipDate();
        }

        return array_unique($dates, SORT_REGULAR);
    }
}
