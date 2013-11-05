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

namespace VIB\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


/**
 * UserAceType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UserAceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "user_ace";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identity', 'user_typeahead', array(
                        'inline' => true,
                        'label_render' => false,
                        'required'  => true,
                        'show_legend' => false,
                        'widget_form_group_attr' => array('class' => 'col-sm-5'),
                        'error_bubbling' => true,
                        'widget_addon_prepend' => array(
                            'icon' => 'user',
                     )))
                ->add('permission', 'choice', array(
                        'inline' => true,
                        'label_render' => false,
                        'required'  => true,
                        'show_legend' => false,
                        'error_bubbling' => true,
                        'widget_form_group_attr' => array('class' => 'col-sm-4'),
                        'choices' => array(
                            0 => 'None',
                            MaskBuilder::MASK_VIEW => 'View',
                            MaskBuilder::MASK_EDIT => 'Edit',
                            MaskBuilder::MASK_OPERATOR => 'Operator',
                            MaskBuilder::MASK_MASTER => 'Master',
                            MaskBuilder::MASK_OWNER => 'Owner',
                     )));
    }
}
