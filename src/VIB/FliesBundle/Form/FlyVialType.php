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

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FlyVialType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyVialType extends AbstractType
{  
    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return "flyvial";
    }
    
    /**
     * Build form
     *
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('setupDate', 'datepicker', array('label' => 'Setup date:'))
                ->add('flipDate', 'datepicker', array('label' => 'Flip date:'))
                ->add('parent', 'text_entity', array(
                        'property'  => 'id',
                        'class'     => 'VIBFliesBundle:FlyVial',
                        'format'    => '%06d',
                        'required'  => false,
                        'label'     => 'Flipped from:'))
                ->add('stock', 'entity_typeahead', array(
                        'property'  => 'name',
                        'class'     => 'VIBFliesBundle:FlyStock',
                        'required'  => false,
                        'label'     => 'Stock:',
                        'data_link' => '/app_dev.php/ajax/stocks/search'))
                ->add('cross', 'text_entity', array(
                        'property'  => 'id',
                        'class'     => 'VIBFliesBundle:FlyCross',
                        'required'  => false,
                        'label'     => 'Cross:'));
    }
    
    /**
     * Set default options
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VIB\FliesBundle\Entity\FlyVial',
        ));
    }
}

?>