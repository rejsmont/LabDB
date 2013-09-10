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
use VIB\CoreBundle\Validator\Constraints\UniqueOwnerIdentity;
use VIB\CoreBundle\Validator\Constraints\UniqueIdentities;

/**
 * AclType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "acl";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_acl', 'collection', array(
                        'type' => new UserAceType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'show_legend' => false,
                        'label' => 'Users',
                        'widget_add_btn' => array('label' => false, 'icon' => 'plus'),
                        'options' => array(
                            'label' => false,
                            'widget_remove_btn' => array('label' => false, 'icon' => 'remove'),
                            'widget_control_group' => false),
                        'constraints' => array(
                            new UniqueOwnerIdentity('Only one user can be the owner.'),
                            new UniqueIdentities('Each user can be specified only once.'))))
                ->add('role_acl', 'collection', array(
                        'type' => new RoleAceType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'show_legend' => false,
                        'label' => 'Groups',
                        'widget_add_btn' => array('label' => false, 'icon' => 'plus'),
                        'options' => array(
                            'label' => false,
                            'widget_remove_btn' => array('label' => false, 'icon' => 'remove'),
                            'widget_control_group' => false),
                        'constraints' => array(
                            new UniqueOwnerIdentity('Only one group can be the owner.'),
                            new UniqueIdentities('Each group can be specified only once.'))));
    }
}
