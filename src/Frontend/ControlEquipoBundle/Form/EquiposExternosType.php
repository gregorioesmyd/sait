<?php

namespace Frontend\ControlEquipoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EquiposExternosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('tipoPropietario')
            ->add('codigoBarra')
            ->add('serial')
            ->add('descripcionEquipo','textarea')
            ->add('equipoMarca',null,array( 'empty_value' => 'Seleccione...'))
            //->add('creado')
            //->add('file')
            //->add('fotoReferencia')
            //->add('estatus')
            ->add('propietarioId','text')
            ->add('tipoPropietario')
            //->add('dependenciaId')

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ControlEquipoBundle\Entity\EquiposExternos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'controlequipobundle_equiposexternos';
    }
}
