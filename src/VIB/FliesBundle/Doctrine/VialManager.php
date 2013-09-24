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

use VIB\CoreBundle\Doctrine\ObjectManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use \VIB\FliesBundle\Entity\Vial;
use \VIB\FliesBundle\Entity\Incubator;
use VIB\FliesBundle\Repository\VialRepository;

/**
 * VialManager is a class used to manage common operations on vials
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialManager extends ObjectManager
{
    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = parent::getRepository($className);

        if (! $repository instanceof VialRepository) {
            throw new \ErrorException('Repository must be an instance of VIB\FliesBundle\Repository\VialRepository');
        } else {
            $repository->setManager($this);
        }

        return $repository;
    }
    
    /**
     * Flip vial(s)
     *
     * @param  \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection $vials
     * @param  boolean                                                              $setSource
     * @param  boolean                                                              $trashSource
     * @return \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection
     * @throws \ErrorException
     */
    public function flip($vials, $setSource = true, $trashSource = false)
    {
        if (($vial = $vials) instanceof Vial) {
            $vialClass = str_replace("Proxies\\__CG__\\","",get_class($vial));
            
            echo "<h1>$vialClass</h1>";
            
            $newVial = new $vialClass($vial,$setSource);
            if ($trashSource) {
                $newVial->setPosition($vial->getPosition());
                $vial->setTrashed(true);
                $this->persist($vial);
            }
            
            echo "<pre>";
            var_dump($newVial);
            echo "</pre>";
            
            $this->persist($newVial);

            return $newVial;
        } elseif ($vials instanceof Collection) {
            $newVials = new ArrayCollection();
            foreach ($vials as $vial) {
                $newVials->add($this->flip($vial,$setSource,$trashSource));
            }

            return $newVials;
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\Vial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Trash vial(s)
     *
     * @param  \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function trash($vials)
    {
        if (($vial = $vials) instanceof Vial) {
            $vial->setTrashed(true);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->trash($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\Vial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * UnTrash vial(s)
     *
     * @param  \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function untrash($vials)
    {
        if (($vial = $vials) instanceof Vial) {
            $vial->setTrashed(false);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->untrash($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\Vial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Mark vial(s) as having their label printed
     *
     * @param  \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection $vials
     * @throws \ErrorException
     */
    public function markPrinted($vials)
    {
        if (($vial = $vials) instanceof Vial) {
            $vial->setLabelPrinted(true);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->markPrinted($vial);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\Vial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Put vials into $incubator
     *
     * @param \VIB\FliesBundle\Entity\Vial|\Doctrine\Common\Collections\Collection $vials
     * @param \VIB\FliesBundle\Entity\Incubator
     * @throws \ErrorException
     */
    public function incubate($vials, Incubator $incubator = null)
    {
        if (($vial = $vials) instanceof Vial) {
            $vial->setStorageUnit($incubator);
            $this->persist($vial);
        } elseif ($vials instanceof Collection) {
            foreach ($vials as $vial) {
                $this->incubate($vial, $incubator);
            }
        } elseif (null === $vials) {
            throw new \ErrorException('Argument 1 must not be null');
        } else {
            throw new \ErrorException('Argument 1 must be an object of class
                VIB\FliesBundle\Entity\Vial or Doctrine\Common\Collections\Collection');
        }
    }

    /**
     * Expand a vial into multiple vials of arbitrary size
     *
     * @param  \VIB\FliesBundle\Doctrine\Vial          $vial
     * @param  integer                                 $count
     * @param  boolean                                 $setSource
     * @param  string                                  $size
     * @return \Doctrine\Common\Collections\Collection
     */
    public function expand(Vial $vial, $count = 1, $setSource = true, $size = null)
    {
        $newVials = new ArrayCollection();
        for ($i = 0; $i < $count; $i++) {
            $newVial = $this->flip($vial, $setSource);
            if (null !== $size) {
                $newVial->setSize($size);
                $this->persist($newVial);
            }
            $newVials->add($newVial);
        }

        return $newVials;
    }
}
