<?php

namespace VIB\FliesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Min;


class FlyCrossNewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "flycross_new";
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cross', new FlyCrossType())
                ->add('number','number', array(
                        'label'       => 'Number of crosses',
                        'constraints' => array(
                            new Min(1))));
    }
}

?>