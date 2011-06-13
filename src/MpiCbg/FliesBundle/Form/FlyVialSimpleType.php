<?php

namespace MpiCbg\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyVialSimpleType extends AbstractType
{  
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('setupDate', 'date')
                ->add('flipDate', 'date');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MpiCbg\FliesBundle\Entity\FlyVial',
        );
    }
}

?>