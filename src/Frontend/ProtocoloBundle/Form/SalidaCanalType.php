<?php

namespace Frontend\ProtocoloBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SalidaCanalType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha','date',array(
                        'attr' => array(
                        'class' => 'form-control datepicker',
                        'style' => 'margin: 0px;  width: 100% !important;',
                        'placeholder'=>'Fecha de llegada'
                    ),
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-yyyy',
                ))
            ->add('hora', 'datetime', array(
                    'attr' => array(
                    'class' => 'form-control timepicker',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Hora llegada'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                ))
            ->add('lugar','textarea',array(
                    'attr' => array(
                    'style' => 'margin: 0px;  width: 100% !important;height: 80px;',
                    'class'=> 'form-control',
                    'placeholder'=>'Lugar donde se pasará buscando el invitado'
                )))
            ->add('invitados','text',array(
                    'attr' => array(
                    'class'=> 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Nombre del Invitado'
                )))
            ->add('tlfInvitado','text',array(
                    'attr' => array(
                    'class'=> 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Ej: 02125551122'
                )))
            ->add('observacion','textarea',array(
                    'attr' => array(
                    'style' => 'margin: 0px; width: 100% !important; height: 80px;',
                    'class'=> 'form-control',
                    'placeholder'=>'Observacion'
                )))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ProtocoloBundle\Entity\SalidaCanal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_protocolobundle_salidacanal';
    }
}
