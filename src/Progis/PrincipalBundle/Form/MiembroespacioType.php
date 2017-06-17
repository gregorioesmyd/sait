<?php

namespace Progis\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MiembroespacioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('nivelorganizacional',null,array('empty_value'=>'Seleccione...','label'=>'Nivel Organizacional'))
            ->add('usuario','entity',array(
                'class' => 'UsuarioBundle:Perfil',
                'attr'=> array('class'=>'chosen-select'),
                'empty_value'=>'Seleccione...',
                'label'=>'Usuarios',
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->leftJoin('x.user', 'xu')
                ->where('xu.isActive=true')
                ->orderBy('x.primerNombre, x.primerApellido',"asc")
            ;}))
            
            ->add('jornadalaboral',null,array('empty_value'=>'Seleccione...','label'=>'Jornada laboral'))

            ->add('activo')
            ->add('mostrarEnReporte',null,array('label'=>'¿Mostrar en reportes generales?'))
            ->add('poseerolgeneral',null,array('label'=>'¿Asignar roles generales?'))
        
            ->add('rolgeneral','entity',array(
                'class' => 'PrincipalBundle:Rolgeneral',
                //'empty_value'=>'Seleccione...',
                'label'=>'Roles Generales',
                'multiple'=>true,
                'expanded'=>true,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->addOrderBy("x.id")
            ;}));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\PrincipalBundle\Entity\Miembroespacio'
        ));
    }

    public function getName()
    {
        return 'progis_principalbundle_miembroespacio';
    }
}
