<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Collection;

class FlyVialExpandType extends AbstractType
{
    public function getName()
    {
        return "FlyVialExpandType";
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('source', 'null_entity', array(
                        'property'     => 'id',
                        'class' => 'VIBFliesBundle:FlyVial',
                        'required' => true,
                        'hidden'    => true,
                        'error_bubbling' => false ))
                ->add('number','number', array('label' => 'Number of vials'));
    }

    public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'source' => new NotNull(),
            'number' => new Min(1),
        ));
                
        return array(
            'validation_constraint' => $collectionConstraint
        );
    }
}

?>