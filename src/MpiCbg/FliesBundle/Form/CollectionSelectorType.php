<?php

namespace MpiCbg\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CollectionSelectorType extends AbstractType
{
    public function getName()
    {
        return "CollectionSelector";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('items', 'collection', array('type' => new CollectionSelectorItemType()))
                ->add('action', 'hidden', array('required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MpiCbg\FliesBundle\Wrapper\Selector\CollectionSelector',
        );
    }
}

?>