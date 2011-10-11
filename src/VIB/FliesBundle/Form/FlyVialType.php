<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyVialType extends AbstractType
{  
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('setupDate', 'date')
                ->add('flipDate', 'date')
                ->add('stock', 'entity', array(
                        'required' => false,
                        'class' => 'VIBFliesBundle:FlyStock'))
                ->add('cross', 'entity', array(
                        'required' => false,
                        'class' => 'VIBFliesBundle:FlyCross'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Entity\FlyVial',
        );
    }
}

?>