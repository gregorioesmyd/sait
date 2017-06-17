<?php

namespace Frontend\ProtocoloBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AeroPuertoType extends AbstractType
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
                    )
                )
            ->add('hora', 'datetime', array(
                    'attr' => array(
                    'class' => 'form-control timepicker',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Hora de llegada'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                    )
                )
            ->add('nombreInvitado','text', array(
                    'attr'  => array(
                    'class' => 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Nombre del Invitado'
                    ))
                )
            ->add('lineaAerea','text', array(
                    'attr'  => array(
                    'class' => 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Nombre de la Linea Áerea'
                    ))
                )
            ->add('numeroVuelo','text', array(
                    'attr'  => array(
                    'class' => 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Número de vuelo'
                    ))
                )
            ->add('lugarDestino','textarea',array(
                    'attr'  => array(
                    'style' => 'margin: 0px;width: 100% !important;height: 40px;',
                    'class' => 'form-control',
                    'placeholder'=>'Lugar donde se llevará al invitado'
                    ))
                )
            ->add('localizador','text', array(
                    'attr'  => array(
                    'class' => 'form-control',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Número localizador'
                    ))
                )
            ->add('aeropuerto','choice',array(
                    'choices'         => array(
                    'Internacional'   => 'Internacional',
                    'Nacional'        => 'Nacional',
                    ),
                    'multiple' => false,
                    'expanded'=>true
                    )
                )
            ->add('paisProcedencia',null,array(
                    'empty_value' => 'Seleccione...',
                    'attr'=> array(
                    'class' => 'form-control chosen-select',
                    'style' => 'width: auto !important;',
                    ))
                 )
            ->add('responsable', 'entity', array(
                    'attr'  => array(
                    'class' => 'form-control chosen-select',
                    'style' => 'width: auto !important;',
                ),
                    'mapped' => true,
                    'class' => 'UsuarioBundle:Perfil',
                    'empty_value' => 'Seleccione...',
                    'query_builder' => function(EntityRepository $e) {
                return $e->createQueryBuilder('a')
                        ->innerJoin('a.user', 'u')
                        ->where('u.isActive = TRUE')
                        ->orderBy('a.id', 'ASC');
            }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ProtocoloBundle\Entity\AeroPuerto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_protocolobundle_aeropuerto';
    }
}
