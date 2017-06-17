<?php

namespace Administracion\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ActualizarDatosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telefonoLocal','text',array('attr'=>array('placeholder'=>'Ej: 02125551122')))
            ->add('telefonoCelular','text',array('attr'=>array('placeholder'=>'Ej: 04265551122')))
            ->add('extension','number',array('invalid_message' => 'Se permiten solo números.','attr'=>array('placeholder'=>'Ej: 000')))
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
                'empty_value'=>'Seleccione...',
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
            
            ->add('file')
            ->add('correoOpcional')
                    
                    ->add('nacionalidad','choice',array(
                        'choices' => array(
                            'morning'   => 'Morning',
                            'afternoon' => 'Afternoon',
                            'evening'   => 'Evening',
                        ),
                        'multiple' => true,
                        
                    ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Administracion\UsuarioBundle\Entity\ActualizarDatos'
        ));
    }

    public function getName()
    {
        return 'usuario_actualizarDatos';
    }
}
