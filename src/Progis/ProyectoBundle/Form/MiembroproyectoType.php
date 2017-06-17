<?php

namespace Progis\ProyectoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MiembroproyectoType extends AbstractType 
{
    
    public function __construct($idNivelMiembroEspacio)
    {
        $this->idNivelMiembroEspacio=$idNivelMiembroEspacio;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $idNivelMiembroEspacio=$this->idNivelMiembroEspacio;
        
        $builder
            //->add('proyecto')
            ->add('miembroespacio','entity',
                array(
                    'class' => 'PrincipalBundle:Miembroespacio',
                    'empty_value'=>'Seleccione...','label'=>'Miembro espacio',
                    
                    'multiple'=>false,
                    'expanded'=>false,
                    'query_builder' => function(EntityRepository $x) use ($idNivelMiembroEspacio){
                    return $x->createQueryBuilder('x')
                    ->where("x.nivelorganizacional in (:id)")
                    ->setParameter('id', $idNivelMiembroEspacio)
                ;}))
                      
            ->add('liderproyecto')
                        
                        
            ->add('rolproyecto','entity',array(
                'class' => 'ProyectoBundle:Rolproyecto',
                //'empty_value'=>'Seleccione...',
                'label'=>'Roles Específicos',
                'multiple'=>false,
                'expanded'=>true,
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
            ;}))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ProyectoBundle\Entity\Miembroproyecto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_proyectobundle_miembroproyecto';
    }
}
