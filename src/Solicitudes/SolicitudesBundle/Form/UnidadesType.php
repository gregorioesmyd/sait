<?php

namespace Solicitudes\SolicitudesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UnidadesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unidad')
            ->add('unidad','entity',array(
                'class' => 'UsuarioBundle:Nivelorganizacional',
                'attr'=> array('class'=>'chosen-select'),
                'empty_value'=>'Seleccione...',
                'label'=>'Unidad',
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->orderBy('x.descripcion')
            ;}))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Solicitudes\SolicitudesBundle\Entity\Unidades'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'solicitudes_solicitudesbundle_unidades';
    }
}
