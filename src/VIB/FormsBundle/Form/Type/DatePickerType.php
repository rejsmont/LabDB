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

use JMS\DiExtraBundle\Annotation as DI;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Bootstrap datepicker form control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class DatePickerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget_addon_append' => array(
                'icon'=> 'calendar'
            ),
            'attr' => array(
                'class' => 'date',
                'data-date-format' => 'dd MM yyyy'
            ),
            'widget' => 'single_text',
            'format' => 'dd MMMM yyyy',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'datepicker';
    }
}
