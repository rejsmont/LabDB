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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use VIB\FliesBundle\Entity\ListCollection;

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
        $factory = $builder->getFormFactory();
        
        $refreshEntities = function ($form, $selections) use ($factory) {
            
            $queryBuilder = function (EntityRepository $repository) use ($selections) {
                if (count($selections) < 1)
                    $selections = array(0);
                $qb = $repository->createQueryBuilder('vial')
                        ->where('vial.id in (:values)')
                        ->setParameter('values', $selections);
                return $qb;
            };
            
            $form->add($factory->createNamed('entity','items',null, array(
                'class'         =>  'VIB\FliesBundle\Entity\FlyVial',
                'multiple'      =>  true,
                'expanded'      =>  true,
                'required'      =>  false,
                'query_builder' =>  $queryBuilder
            )));
        };
 
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (DataEvent $event) use ($refreshEntities) {
            
            $form = $event->getForm();
            $data = $event->getData();
 
            if($data == null)
                $refreshEntities($form, array());
 
            if($data instanceof ListCollection) {
                $values = array();
                foreach ($data->getItems() as $item) {
                    $values[] = $item->getId();
                }
                                
                $refreshEntities($form, $values);
            }
        });
 
        $builder->addEventListener(FormEvents::PRE_BIND, function (DataEvent $event) use ($refreshEntities) {
            
            $form = $event->getForm();
            $data = $event->getData();
                
            if (is_array($data)) {
                if (isset($data['items'])) {
                    
                    $values = array();
                    
                    foreach ($data['items'] as $item) {
                        $values[] = $item;
                    }
                    
                    $refreshEntities($form, $values);
                }
            }
        });
    }
    
    /**
     * Get default options
     *
     * @param array $options
     * @return array $options
     */
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'VIB\FliesBundle\Entity\ListCollection');
    }
}

?>