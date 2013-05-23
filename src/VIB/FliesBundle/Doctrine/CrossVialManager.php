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

namespace VIB\FliesBundle\Doctrine;

use Doctrine\Common\Collections\Collection;

use VIB\FliesBundle\Repository\CrossVialRepository;
use VIB\FliesBundle\Entity\CrossVial;

/**
 * CrossVialManager is a class used to manage common operations on cross vials
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialManager extends VialManager
{
    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = parent::getRepository($className);

        if (! $repository instanceof CrossVialRepository) {
            throw new \ErrorException('Repository must be an instance of VIB\FliesBundle\Repository\CrossVialRepository');
        } else {
            $repository->setManager($this);
        }

        return $repository;
    }

    /**
     * Mark cross(es) as sterile and trash it (them)
     *
     * @param  \VIB\FliesBundle\Entity\CrossVial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function markSterile($vials)
    {
        if (($vial = $vials) instanceof CrossVial) {
            $vial->setSterile(true);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->markSterile($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\CrossVial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Mark cross(es) as successful
     *
     * @param  \VIB\FliesBundle\Entity\CrossVial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function markSuccessful($vials)
    {
        if (($vial = $vials) instanceof CrossVial) {
            $vial->setSuccessful(true);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->markSuccessful($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\CrossVial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Mark cross(es) as failed
     *
     * @param  \VIB\FliesBundle\Entity\CrossVial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function markFailed($vials)
    {
        if (($vial = $vials) instanceof CrossVial) {
            $vial->setSuccessful(false);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->markFailed($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\CrossVial or Doctrine\Common\Collections\Collection');
        }
    }
}
