<?php

namespace Solicitudes\SolicitudesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RolesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('administradores')
            ->add('consulta')
            ->add('solicitudes')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Solicitudes\SolicitudesBundle\Entity\Roles'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'solicitudes_solicitudesbundle_roles';
    }
}
