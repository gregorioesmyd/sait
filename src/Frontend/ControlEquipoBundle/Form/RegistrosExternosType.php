<?php

namespace Frontend\ControlEquipoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrosExternosType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('fechaEntrada')
            //->add('fechaSalida')
            //->add('estatus')
            ->add('propietarioId')
            ->add('equipo')
            //->add('dependencia')
            ->add('dependencia',null,array('empty_value' => 'Dependencia'))
            ->add('tipo')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ControlEquipoBundle\Entity\RegistrosExternos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_controlequipobundle_registrosexternos';
    }
}
