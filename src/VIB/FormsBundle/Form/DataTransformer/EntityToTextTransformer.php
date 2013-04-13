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

namespace VIB\FormsBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms entity into its string representation
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityToTextTransformer implements DataTransformerInterface
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * @var Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $class;

    /**
     * @var Symfony\Component\Form\Util\PropertyPath
     */
    protected $propertyPath;

    /**
     * @var string
     */
    protected $format;

    /**
     * Construct EntityToTextTransformer
     *
     * @param Doctrine\Common\Persistence\ObjectManager $om       The object manager to use
     * @param string                                    $class    Class of the entity
     * @param string                                    $property Property to lookup
     * @param string                                    $format   sprintf-compatible format string
     */
    public function __construct(ObjectManager $om, $class, $property = null, $format = null)
    {
        $this->om = $om;
        $this->class = $class;
        $this->propertyPath = (null !== $property) ? new PropertyPath($property) : null;
        $this->format = $format;
    }

    /**
     * Transform entity into string value
     *
     * @param  object $entity
     * @return string
     */
    public function transform($entity)
    {

        if (null === $entity || '' === $entity) {
            return '';
        }

        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }

        if (null !== $this->propertyPath) {
            $propertyAccessor = PropertyAccess::getPropertyAccessor();
            $value = (string) ($propertyAccessor->getValue($entity, $this->propertyPath));
        } else {
            $value = (string) ($entity);
        }

        if (null === $this->format)
          return $value;
        else
          return sprintf($this->format, $value);
    }

    /**
     * Transform string into entity
     *
     * @param  mixed  $key
     * @return object
     */
    public function reverseTransform($key)
    {
        if ('' === $key || null === $key || 'null' === $key) {
            return null;
        }

        if (!is_string($key)) {
            return null;
        }

        $property = (string) $this->propertyPath;
        if ($property) {
            $entity = $this->om->getRepository($this->class)->findOneBy(array($property => $key));
        } else {
            $entity = $this->om->getRepository($this->class)->findOneById($key);
        }
        if ($entity === null) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
        }

        return $entity;
    }
}
