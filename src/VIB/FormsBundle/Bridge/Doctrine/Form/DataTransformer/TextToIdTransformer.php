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

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Description of TextToIdTransformer
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class TextToIdTransformer implements DataTransformerInterface
{
 
    protected $em;
    protected $class;
    protected $propertyPath;
 
    public function __construct(EntityManager $em, $class, $property = null)
    {
        $this->em = $em;
        $this->class = $class;
 
        if ($property) {
             $this->propertyPath = new PropertyPath($property);
        }
    }
 
    public function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return 'null';
        }
 
        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }
 
       if ($this->propertyPath) {
       // If the property option was given, use it
            $value = $this->propertyPath->getValue($entity);
       } else {
           // Otherwise expect a __toString() method in the entity
           $value = (string)$entity;
       }
       return $value;
    }
 
    public function reverseTransform($key)
    {
          if ('' === $key || null === $key) {
          return null;
          }
 
         if (!is_string($key))
         {
             return null;
         }
 
         if (!is_numeric($key))
         {
             throw new UnexpectedTypeException($key, 'numeric');
         }
 
         $entity = $this->em->getRepository($this->class)->findOneById($key);
 
         if ($entity === null) {
             throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
         }
 
        return $entity;
    }
}

?>
