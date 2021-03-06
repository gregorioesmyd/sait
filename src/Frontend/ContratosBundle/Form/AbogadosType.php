<?php

namespace Frontend\ContratosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AbogadosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('nombre', 'textarea')
           ->add('estatus')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ContratosBundle\Entity\Abogados'
        ));
    }

    public function getName()
    {
        return 'frontend_contratosbundle_abogadostype';
    }
}
