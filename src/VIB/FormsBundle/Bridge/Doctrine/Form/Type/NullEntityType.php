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

namespace VIB\FormsBundle\Bridge\Doctrine\Form\Type;

use Doctrine\ORM\EntityManager;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

use VIB\FormsBundle\Bridge\Doctrine\Form\DataTransformer\TextToIdTransformer;



/**
 * Description of NullEntityType
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class NullEntityType extends AbstractType
{
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
 
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->prependClientTransformer(new TextToIdTransformer(
            $this->registry->getEntityManager($options['em']),
            $options['class'],
            $options['property']
        ));
    }
 
    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em'                => null,
            'class'             => null,
            'property'          => null,
            'text'              => false,
            'hidden'            => false,
        );
 
        $options = array_replace($defaultOptions, $options);
 
        return $options;
    }
 
    public function getParent(array $options)
    {
         if ($options['hidden']) {
            return 'hidden';
         }
         if ($options['text']) {
            return 'text';
         }
         return 'choice';
    }
 
    public function getName()
    {
        return 'null_entity';
    }
}

?>
