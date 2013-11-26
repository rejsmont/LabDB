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
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Bootstrap entity typeahead form control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class EntityTypeaheadType extends AbstractType
{
    /**
     * @var Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * Construct EntityTypeaheadType
     * 
     * @DI\InjectParams({
     *     "router" = @DI\Inject("router")
     * })
     * 
     * @param Symfony\Component\Routing\Router            $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data_link = $options['data_link'];
        $data_route = $options['data_route'];
        $data_route_options = $options['data_route_options'];

        if (null === $data_link) {
            if (null === $data_route) {
                $data_link = $this->router->generate('VIBFormsBundle_ajax_choices',
                    array('class' => $options['class'],
                          'property' => $options['property']));
            } else {
                $data_link = $this->router->generate($data_route, $data_route_options);
            }
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
            'data_link' => null,
            'data_route' => null,
            'data_route_options' => array()
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'entity_typeahead';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text_entity';
    }
}
