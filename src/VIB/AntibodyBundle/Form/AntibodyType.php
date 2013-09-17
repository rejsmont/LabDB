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

namespace VIB\AntibodyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * AntibodyType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AntibodyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "antibody";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('antigen', 'text', array(
                        'label'     => 'Antigen'))
                ->add('targetSpecies', 'text', array(
                        'label'     => 'Target species'))
                ->add('hostSpecies', 'text', array(
                        'label'     => 'Host species'))
                ->add('order', 'choice', array(
                        'label'     => 'Type',
                        'required'  => true,
                        'choices' => array(
                            'primary' => 'Primary',
                            'secondary' => 'Secondary',
                        )))
                ->add('type', 'choice', array(
                        'label'     => false,
                        'required'  => true,
                        'choices' => array(
                            'monoclonal' => 'Monoclonal',
                            'polyclonal' => 'Polyclonal',
                        )))
                ->add('class', 'choice', array(
                        'label'     => false,
                        'required'  => true,
                        'choices' => array(
                            'IgG' => 'IgG',
                            'IgM' => 'IgM',
                            'nanobody' => 'Nanobody',
                        )))
                ->add('clone', 'text', array(
                        'label'     => 'Clone'))
                ->add('size', 'number', array(
                        'label'     => 'Size'))
                ->add('notes', 'textarea', array(
                        'label' => 'Notes',
                        'required' => false))
                ->add('vendor', 'text', array(
                        'label' => 'Vendor',
                        'required' => false,
                        'attr' => array('class' => 'input-block-level')))
                ->add('infoURL', 'url', array(
                        'label' => 'Info URL',
                        'required' => false,
                        'attr' => array('class' => 'input-block-level',
                                        'placeholder' => 'Paste address here')))
                ->add('applications', 'collection', array(
                        'type' => new ApplicationType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'prototype' => true,
                        'show_legend' => false,
                        'label' => 'Applications',
                        'widget_add_btn' => array('label' => false, 'icon' => 'plus'),
                        'options' => array(
                            'label' => false,
                            'widget_remove_btn' => array('label' => false, 'icon' => 'remove'),
                            'widget_control_group' => false)));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VIB\AntibodyBundle\Entity\Antibody'
        ));
    }
}
