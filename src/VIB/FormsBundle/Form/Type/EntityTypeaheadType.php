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

namespace VIB\FormsBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityTypeaheadType extends TextEntityType
{
    /**
     * @var Symfony\Component\Routing\Router
     */
    protected $router;

    
    /**
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry, Router $router) {
        $this->registry = $registry;
        $this->router = $router;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data_link = $options['data_link'];
        
        if (null === $data_link) {
            $data_link = $this->router->generate('VIBFormsBundle_ajax_choices',
                    array('class' => $options['class'],
                          'property' => $options['property']));
        }
        
        $view->vars['data_link'] = $data_link;
    }
  
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
      parent::setDefaultOptions($resolver);
      
      $resolver->setDefaults(array(
            'data_link' => null
        ));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'entity_typeahead';
    }
}

?>
