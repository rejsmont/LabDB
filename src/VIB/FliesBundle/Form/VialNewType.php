<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Range;


class CrossVialNewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "vial_new";
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vial', new VialType())
                ->add('number','number', array(
                        'label'       => 'Number of vials',
                        'constraints' => array(
                            new Range(array('min' => 1)))));
    }
}

?>
