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

namespace VIB\FliesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * VialOptionsType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VialOptionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "vial_options";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('size', 'choice', array(
                        'choices' => array(
                            'small'  => 'small',
                            'medium' => 'medium',
                            'large'  => 'large'
                        ),
                        'expanded'    => true,
                        'label'       => 'Vial size',
                        'required'    => false,
                        'empty_value' => false,
                        'horizontal' => true
                    )
                )
                ->add('food', 'food', array(
                        'label'    => 'Food type',
                        'required' => false,
                        'horizontal' => true
                    )
                );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true
        ));
    }
}
