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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use VIB\FormsBundle\Form\DataTransformer\RoleToTextTransformer;

/**
 * Bootstrap role typeahead form control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RoleTypeaheadType extends TypeaheadType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new RoleToTextTransformer();
        $builder->addModelTransformer($transformer);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'data_route' => 'vib_role_ajax_choices'
        ));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'role_typeahead';
    }
}
