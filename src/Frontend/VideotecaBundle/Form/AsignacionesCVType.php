<?php

namespace Frontend\VideotecaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AsignacionesCVType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('idUsuarioSolicitante', null, array(
                'empty_value' => 'Seleccione...',
                'label' => 'Usuario Solicitante:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            
            ->add('cantidad', null, array(
                'label' => 'Cantidad:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))

            ->add('motivo', null, array(
                'label' => 'Motivo:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\AsignacionesCV'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_asignacionescv';
    }
}
