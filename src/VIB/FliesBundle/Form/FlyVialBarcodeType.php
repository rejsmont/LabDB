<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyVialBarcodeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('parentBarcode', 'number', array('required' => false))
                ->add('entity', new FlyVialType());
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Wrapper\Barcode\FlyVial',
        );
    }
}

?>