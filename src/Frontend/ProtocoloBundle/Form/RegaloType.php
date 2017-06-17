<?php

namespace Frontend\ProtocoloBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegaloType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha','date',array(
                        'attr'  => array(
                        'class' => 'form-control datepicker',
                        'style' => 'margin: 0px;  width: 100% !important;',
                        'placeholder'=>'Fecha de la entrega'
                    ),
                    'widget'   => 'single_text',
                    'format'   => 'dd-MM-yyyy',
                ))
            ->add('nroRegalos','text', array(
                    'attr'  => array(
                    'style' => 'width: 100px;',
                    'class' =>'form-control',
                    'placeholder'=>'N° regalos'
                    )))
            ->add('dirigido','textarea',array(
                    'attr'  => array(
                    'style' => 'margin: 0px; height: 122px;',
                    'class' =>'form-control',
                    'placeholder'=>'A quien van dirigidos los regalos'
                )))
            ->add('tipoRegalo','choice',array(
                    'choices'              => array(
                    'Corporativo VIP'      => 'Corporativo VIP',
                    'Corporativo Masivo'   => 'Corporativo Masivo',
                ),
                'multiple' => false,
                'expanded' =>true
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ProtocoloBundle\Entity\Regalo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_protocolobundle_regalo';
    }
}
