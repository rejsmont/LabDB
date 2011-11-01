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
use Symfony\Component\Form\FormBuilder;

/**
 * FlyVialSelectType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class FlyVialSelectType extends AbstractType
{  
    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return "FlyVialSelectType";
    }
    
    /**
     * Build form
     *
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('setupDate', 'date')
                ->add('flipDate', 'date')
                ->add('parent', 'null_entity', array(
                        'required' => false,
                        'class' => 'VIBFliesBundle:FlyVial',
                        'text' => true))
                ->add('stock', 'entity', array(
                        'required' => false,
                        'class' => 'VIBFliesBundle:FlyStock'))
                ->add('cross', 'entity', array(
                        'required' => false,
                        'class' => 'VIBFliesBundle:FlyCross'));
    }
    
    /**
     * Get default options
     *
     * @param array $options
     * @return array $options
     */
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'VIB\FliesBundle\Entity\FlyVial');
    }
}

?>