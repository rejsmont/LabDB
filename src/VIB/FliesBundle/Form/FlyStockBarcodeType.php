<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyStockBarcodeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('sourceCrossBarcode', 'number', array('required' => false))
                ->add('entity', new FlyStockType());
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Wrapper\Barcode\FlyStock',
        );
    }
}

?>