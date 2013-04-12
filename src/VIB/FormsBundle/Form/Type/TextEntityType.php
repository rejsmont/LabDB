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

namespace VIB\FormsBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use VIB\FormsBundle\Form\DataTransformer\EntityToTextTransformer;


/**
 * Entity as text input control
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class TextEntityType extends AbstractType
{
    /**
     * @var Doctrine\Common\Persistence\ManagerRegistry
     */
    protected $registry;

    
    /**
     * Construct TextEntityType
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToTextTransformer(
                $options['em'],
                $options['class'],
                $options['property'],
                $options['format']);

        $builder->addModelTransformer($transformer);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;

        $emNormalizer = function (Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['class']);

            if (null === $em) {
                throw new FormException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. ' .
                    'Did you forget to map it?',
                    $options['class']
                ));
            }

            return $em;
        };

        $resolver->setDefaults(array(
            'em'                => null,
            'property'          => null,
            'query_builder'     => null,
            'format'            => null
        ));

        $resolver->setRequired(array('class'));

        $resolver->setNormalizers(array(
            'em' => $emNormalizer,
        ));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'text_entity';
    }
}

?>
