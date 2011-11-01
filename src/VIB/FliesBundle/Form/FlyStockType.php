<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyStockType extends AbstractType
{
    public function getName()
    {
        return "FlyStockType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Entity\FlyStock',
        );
    }
}

?>