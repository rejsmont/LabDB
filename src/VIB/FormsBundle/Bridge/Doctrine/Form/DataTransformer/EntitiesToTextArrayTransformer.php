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

namespace VIB\FormsBundle\Bridge\Doctrine\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Description of EntitiesToTextArrayTransformer
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntitiesToTextArrayTransformer implements DataTransformerInterface
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    /**
     * 
     * @var Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $class;
    
    /**
     *
     * @var Symfony\Component\Form\Util\PropertyPath
     */
    protected $propertyPath;
    
    /**
     * Construct EntitiesToTextArrayTransformer
     * 
     * @param Doctrine\ORM\EntityManager $em
     * @param Doctrine\ORM\Mapping\ClassMetadata $class
     * @param string $property 
     */
    public function __construct(EntityManager $em, $class, $property = null) {
        $this->em = $em;
        $this->class = $class;
 
        if ($property) {
             $this->propertyPath = new PropertyPath($property);
        }
    }
 
    /**
     * Transforms entities into array of keys
     *
     * @param Doctrine\Common\Collections\Collection
     * @return array
     */
    public function transform($collection) {
        
        if (null === $collection) {
            return array();
        }
 
        if (!($collection instanceof Collection)) {
            throw new UnexpectedTypeException($collection, 'Doctrine\Common\Collections\Collection');
        }
 
        $array = array();
        
        foreach ($collection as $entity) {
            if ($this->propertyPath) {
                $value = $this->propertyPath->getValue($entity);
            } else {
                $value = (string)$entity;
            }
            $array[] = is_numeric($value) ? (int) $value : $value;
        }
        
        return $value;
    }
    
    /**
     * Transform array of numeric keys into collection of entities
     * 
     * @param array $keys
     * @return Doctrine\Common\Collections\Collection
     */
    public function reverseTransform($keys)
    {
        $collection = new ArrayCollection();

        if ('' === $keys || null === $keys) {
            return $collection;
        }

        if (!is_array($keys)) {
            throw new UnexpectedTypeException($keys, 'array');
        }

        $notFound = array();
        
        foreach ($keys as $key) {
            
            if (!is_numeric($key)) {
                throw new UnexpectedTypeException($key, 'numeric');
            }
            
            if ($entity = $this->em->getRepository($this->class)->findOneById($key)) {
                $collection->add($entity);
            } else {
                $notFound[] = $key;
            }
        }
        
        if (count($notFound) > 0) {
            throw new TransformationFailedException(
                    sprintf('The entities with keys "%s" could not be found', implode('", "', $notFound)));
        }

        return $collection;
    }
}

?>
