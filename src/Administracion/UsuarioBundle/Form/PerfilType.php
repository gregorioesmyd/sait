<?php

namespace Administracion\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('primerNombre')
            ->add('segundoNombre')
            ->add('primerApellido')
            ->add('segundoApellido')
            ->add('fechanacimiento','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
             ))
            ->add('cedula')
            ->add('extension','text')
            ->add('jefe')
            ->add('file')
            ->add('nacionalidad','choice',array(
                'choices' => array(
                    'V'   => 'Venezolana',
                    'E' => 'Extranjera',
                ),
                'multiple' => false,
                'expanded'=>true

            ))
            ->add('correoOpcional',null,array('attr'=>array('placeholder'=>'Ej: abc@gmail.com')))
            ->add('direccionHabitacion','textarea')
            ->add('telefonoLocal','text',array('invalid_message' => 'Se permiten solo números.','attr'=>array('placeholder'=>'Ej: 02125551122')))
            ->add('telefonoCelular','text',array('invalid_message' => 'Se permiten solo números.','attr'=>array('placeholder'=>'Ej: 04265551122')))
            ->add('extension','number',array('invalid_message' => 'Se permiten solo números.','invalid_message' => 'Se permiten solo números.','attr'=>array('placeholder'=>'Ej: 000')))
            ->add('abreviado',null,array('attr'=>array('placeholder'=>'Ej: *811234')))
            ->add('estado','entity',array(
                'class' => 'UsuarioBundle:Estado',
                'property'=>'estado',
                'attr'=> array('class'=>'chosen-select'),
                'empty_value'=>'Seleccione...',
                'label'=>'Estado',
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->orderBy('x.estado',"asc")
            ;}))
            ->add('ciudad','entity',array(
                'class' => 'UsuarioBundle:Ciudad',
                'property'=>'ciudad',
                'attr'=> array('class'=>'chosen-select'),
                'empty_value'=>'Seleccione un estado',
                'label'=>'Estado',
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->orderBy('x.ciudad',"asc")
            ;}))
                
            ->add('nivelorganizacional','entity',array(
                'class' => 'UsuarioBundle:Nivelorganizacional',
                'property'=>'descripcion',
                'attr'=> array('class'=>'chosen-select'),
                'empty_value'=>'Seleccione...',
                'label'=>'Niveles',
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->orderBy('x.descripcion',"asc")
            ;}))
            
            #->add('user')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Administracion\UsuarioBundle\Entity\Perfil'
        ));
    }

    public function getName()
    {
        return 'administracion_usuariobundle_perfiltype';
    }
}
