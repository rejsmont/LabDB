<?php

namespace MpiCbg\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CollectionSelectorItemType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('selected', 'checkbox', array('required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelectorItem',
        );
    }
}

?>