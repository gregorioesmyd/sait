<?php

namespace Frontend\ProtocoloBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReunionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $refigerios=array('Cafe'=> 'Café','Agua'=> 'Agua','Sin refrigerio'=> 'Sin refrigerio');
        
        $builder
            ->add('fecha','date',array(
                        'attr'  => array(
                        'class' => 'form-control datepicker',
                        'style' => 'margin: 0px;  width: 100% !important;',
                        'placeholder'=>'Fecha de la reunión'
                    ),
                    'widget'   => 'single_text',
                    'format'   => 'dd-MM-yyyy',
                ))
            ->add('hora', 'datetime', array(
                    'attr'  => array(
                    'class' => 'form-control timepicker',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Hora de la reunión'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                ))
            ->add('lugar','textarea',array(
                    'attr'  => array(
                    'style' => 'margin: 0px; height: 73px;',
                    'class' =>'form-control',
                    'placeholder'=>'Oficina donde se realizará la reunión'
                )))
            ->add('numeroPersonas','text', array(
                    'attr'  => array(
                    'style' => 'width: 100px;',
                    'class' =>'form-control',
                    'placeholder'=>'N° personas'
                    )))
            ->add('refrigerio','choice',array(
                    'choices'=>$refigerios,
                    'multiple' => true,
                    'expanded' => true,
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ProtocoloBundle\Entity\Reunion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_protocolobundle_reunion';
    }
}
