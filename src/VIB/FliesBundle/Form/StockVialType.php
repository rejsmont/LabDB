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

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * StockVialType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockVialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "stockvial";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('setupDate', 'datepicker', array('label' => 'Setup date'))
                ->add('flipDate', 'datepicker', array('label' => 'Flip date', 'required'  => false))
                ->add('stock', 'entity_typeahead', array(
                        'property'  => 'name',
                        'class'     => 'VIBFliesBundle:Stock',
                        'label'     => 'Stock'))
                ->add('notes', 'textarea', array(
                        'label' => 'Notes',
                        'required' => false))
                ->add('sourceVial', 'text_entity', array(
                        'property'  => 'id',
                        'class'     => 'VIBFliesBundle:StockVial',
                        'format'    => '%06d',
                        'required'  => false,
                        'label'     => 'Flipped from',
                        'attr' => array('class' => 'barcode')))
                ->add('size', 'choice', array(
                        'choices'   => array('small' => 'small',
                                             'medium' => 'medium',
                                             'large' => 'large'),
                        'expanded'  => true,
                        'label'     => 'Vial size',
                        'required'  => false,
                        'attr'      => array('class' => 'input-text')))
                ->add('trashed', 'checkbox', array(
                        'label' => '',
                        'required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VIB\FliesBundle\Entity\StockVial',
        ));
    }
}
