<?php

namespace Frontend\ProtocoloBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Frontend\ProtocoloBundle\Form\TransporteType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class InvitadosType extends AbstractType
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
                    'placeholder'=>'Hora de llegada'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                ))
            ->add('invitados','text',array(
                    'attr' => array(
                    'class' => 'form-control',
                    'data-role' => 'tagsinput',
                    'placeholder'=>'Nombre de los invitados'
                )))
            ->add('transportePersonal','choice',array(
                    'choices' => array(
                        'true'   => 'Si',
                        'false' => 'No',
                    ),
                    'multiple' => false,
                    'expanded'=>true
                    ))
            ->add('horaEstudio', 'datetime', array(
                    'attr' => array(
                    'class' => 'form-control timepicker',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Hora de la entrevista'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                ))
            ->add('wifi','choice',array(
                'choices' => array(
                    'true'   => 'Si',
                    'false' => 'No',
                ),
                'multiple' => false,
                'expanded'=>true
                ))
            ->add('motivo','textarea',array(
                    'attr' => array(
                    'style' => 'margin: 0px;height: 121px;',
                    'class' => 'form-control',
                    'placeholder'=>'Motivo o programa al cual asistirá'
                ))) 
            ->add('responsableEntrevista', 'entity', array(
                    'attr' => array(
                        'class' => 'form-control chosen-select'
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

            ->add('horaBusqueda', 'datetime', array(
                    'attr' => array(
                    'class' => 'form-control timepicker',
                    'style' => 'margin: 0px;  width: 100% !important;',
                    'placeholder'=>'Hora que se buscará el invitado'
                    ),
                    'widget' => 'single_text',
                    'format' => 'HH:mm:ss',
                    'mapped' => false
                ))
             ->add('tlfContacto', null, array(
                    'attr'  => array(
                        'style' => 'width:118px;',
                        'class' =>'form-control',
                        'style' => 'margin: 0px;  width: 100% !important;',
                        'placeholder'=>'Número de contacto'
                        ),
                    'mapped' => false
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ProtocoloBundle\Entity\Invitados'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_protocolobundle_invitados';
    }
}
