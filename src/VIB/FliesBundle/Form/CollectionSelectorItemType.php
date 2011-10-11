<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CollectionSelectorItemType extends AbstractType
{
    public function getName()
    {
        return "CollectionSelectorItem";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('selected', 'checkbox', array('required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Wrapper\Selector\CollectionSelectorItem',
        );
    }
}

?>