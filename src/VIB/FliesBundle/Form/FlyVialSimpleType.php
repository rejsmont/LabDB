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

class FlyVialSimpleType extends AbstractType
{  
    public function getName()
    {
        return "FlyVialSimpleType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('setupDate', 'date', array(
                        'attr' => array('class' => 'date'),
                        'widget' => 'single_text',
                        'format' => 'dd MMM yyyy'))
                ->add('flipDate', 'date', array(
                        'attr' => array('class' => 'date'),
                        'widget' => 'single_text',
                        'format' => 'dd MMM yyyy'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Entity\FlyVial',
        );
    }
}

?>
