<?php

namespace Solicitudes\SolicitudesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ResponsablesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('responsable','entity',array(
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
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Solicitudes\SolicitudesBundle\Entity\Responsables'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'solicitudes_solicitudesbundle_responsables';
    }
}
