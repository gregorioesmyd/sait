<?php

namespace Progis\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ticketsCreadosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            
            ->add('desde','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha inicio estimada'       ))
            ->add('hasta','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha fin estimada'));
            
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ReporteBundle\Entity\TicketsCreados'
        ));
    }

    public function getName()
    {
        return 'progis_reportebundle_tc';
    }
}
