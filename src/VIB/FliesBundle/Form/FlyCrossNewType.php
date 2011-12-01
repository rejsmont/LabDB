<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\Collection;

class FlyCrossNewType extends AbstractType
{
    public function getName()
    {
        return "FlyCrossNewType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('cross', new FlyCrossType())
                ->add('number','number', array('label' => 'Number of crosses'));
    }

    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'number' => new Min(1),
        ));
        
        return array(
            'validation_constraint' => $collectionConstraint
        );
    }
}

?>