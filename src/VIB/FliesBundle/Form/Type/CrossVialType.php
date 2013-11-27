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
 * CrossVialType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class CrossVialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "crossvial_basic";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('setupDate', 'datepicker', array(
                        'label' => 'Setup date'
                    )
                )
                ->add('flipDate', 'datepicker', array(
                        'label'    => 'Check date',
                        'required' => false
                    )
                )
                ->add('virgin', 'text_entity', array(
                        'property' => 'id',
                        'class'    => 'VIBFliesBundle:Vial',
                        'format'   => '%06d',
                        'label'    => 'Virgin vial',
                        'attr'     => array('class' => 'barcode'),
                        'widget_addon_append' => array(
                            'icon' => 'qrcode'
                        )
                    )
                )
                ->add('virginName', 'text', array(
                        'label'    => 'Virgin genotype',
                        'required' => false
                    )
                )
                ->add('male', 'text_entity', array(
                        'property' => 'id',
                        'class'    => 'VIBFliesBundle:Vial',
                        'format'   => '%06d',
                        'label'    => 'Male vial',
                        'attr'     => array('class' => 'barcode'),
                        'widget_addon_append' => array(
                            'icon' => 'qrcode'
                        )
                    )
                )
                ->add('maleName', 'text', array(
                        'label'    => 'Male genotype',
                        'required' => false
                    )
                )
                ->add('notes', 'textarea', array(
                        'label'    => 'Notes',
                        'required' => false
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
            )
        );
    }
}
