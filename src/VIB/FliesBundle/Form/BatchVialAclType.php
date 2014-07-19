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

use VIB\CoreBundle\Form\AclType;

/**
 * BatchVialAclType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class BatchVialAclType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "batchvialacl";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vials', 'collection', array(
                        'type'   => 'hidden_entity',
                        'allow_add' => true,
                        'options' => array(
                            'class' =>  'VIB\FliesBundle\Entity\Vial'
                        ),
                        'attr'     => array('class' => 'hidden'),
                        'horizontal' => false,
                        'label_render' => false,
                        'widget_form_group' => false
                    )
                )
                ->add('acl', new AclType(), array(
                        'horizontal' => false,
                        'label_render' => false,
                        'widget_form_group' => false
                    )
                );
    }
}
