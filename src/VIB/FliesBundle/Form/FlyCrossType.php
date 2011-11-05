<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FlyCrossType extends AbstractType
{
    public function getName()
    {
        return "FlyCrossType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('maleName', 'text', array('label' => 'Male name'))
                ->add('virginName', 'text', array('label' => 'Virgin name'))
                ->add('vial', new FlyVialSimpleType())
                ->add('male', 'null_entity', array(
                        'property'     => 'id',
                        'class' => 'VIBFliesBundle:FlyVial',
                        'required' => false,
                        'hidden'    => true))
                ->add('virgin', 'null_entity', array(
                        'property'     => 'id',
                        'class' => 'VIBFliesBundle:FlyVial',
                        'required' => false,
                        'hidden'    => true));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'VIB\FliesBundle\Entity\FlyCross',
        );
    }
}

?>