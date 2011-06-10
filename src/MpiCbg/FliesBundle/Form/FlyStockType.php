<?php

namespace MpiCbg\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyStockType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MpiCbg\FliesBundle\Entity\FlyStock',
        );
    }
}

?>