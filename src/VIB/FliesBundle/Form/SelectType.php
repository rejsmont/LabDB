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

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

/**
 * SelectType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SelectType extends AbstractType
{  
    protected $entityClass;
    
    /**
     * Construct SelectType
     * 
     */ 
    public function __construct($entityClass = null)
    {
        $this->entityClass = $entityClass;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return "select";
    }
    
    /**
     * Build form
     *
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', 'hidden')
                ->add('items', 'collection', array(
                      'type'   => 'entity',
                      'allow_add' => true,
                      'options' => array(
                          'class' =>  $this->entityClass)))
                ->add('incubator', 'hidden_entity', array(
                      'property'     => 'name',
                      'class' => 'VIBFliesBundle:Incubator',
                      'attr' => array('class' => 'input-medium')));
    }
    
    /**
     * Get default options
     *
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array();
    }
}

?>