<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class SearchType extends AbstractType
{
    public function getName()
    {
        return "SearchType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('term', 'search', array('label' => 'Search:'))
                ->add('filter','choice', array(
                            'choices'   => array('s' => 'stocks', 'c' => 'crosses'),
                            'empty_value' => 'everything',
                            'required'  => false,
                        ));
    }

    public function getDefaultOptions(array $options)
    {        
        return array();
    }
}

?>