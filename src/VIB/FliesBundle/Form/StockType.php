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
 * StockType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class StockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "stock";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                        'label' => 'Name',
                        'horizontal' => true
                    )
                )
                ->add('genotype', 'genotype_typeahead', array(
                        'label'    => 'Genotype',
                        'attr'     => array(
                            'data-id-source' => 'cross-id',
                            'class'          => 'fb-genotype'
                        ),
                        'data_route' => 'vib_flies_ajax_genotypes',
                        'required'   => false,
                        'horizontal' => true
                    )
                )
                ->add('source_cross', 'text_entity', array(
                        'property' => 'id',
                        'class'    => 'VIBFliesBundle:CrossVial',
                        'format'   => '%06d',
                        'required' => false,
                        'label'    => 'Source cross',
                        'attr' => array('class' => 'barcode cross-id'),
                        'widget_addon_append' => array(
                            'icon' => 'qrcode'
                        ),
                        'horizontal' => true
                    )
                )
                ->add('notes', 'textarea', array(
                        'label'    => 'Notes',
                        'required' => false,
                        'horizontal' => true
                    )
                )
                ->add('vendor', 'typeahead', array(
                        'label'    => 'Vendor',
                        'required' => false,
                        'attr' => array(
                            'class' => 'fb-vendor'
                        ),
                        'data_route' => 'vib_flies_ajax_flybasevendor',
                        'horizontal' => true
                    )
                )
                ->add('vendorId', 'typeahead', array(
                        'label'    => 'Vendor ID',
                        'required' => false,
                        'attr' => array(
                            'class' => 'fb-vendorid'
                        ),
                        'data_route' => 'vib_flies_ajax_flybasestock',
                        'horizontal' => true
                    )
                )
                ->add('infoURL', 'url', array(
                        'label'    => 'Info URL',
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'Paste address here',
                            'class'       => 'fb-link'
                        ),
                        'horizontal' => true
                    )
                )
                ->add('verified', 'checkbox', array(
                        'label'    => '',
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
            'data_class' => 'VIB\FliesBundle\Entity\Stock'
        ));
    }
}
