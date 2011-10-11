<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyCrossBarcodeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('maleBarcode', 'number', array('required' => true))
                ->add('virginBarcode', 'number', array('required' => true))
                ->add('entity', new FlyCrossType());
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Wrapper\Barcode\FlyCross',
        );
    }
}

?>