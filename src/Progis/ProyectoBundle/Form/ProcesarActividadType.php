<?php

namespace Progis\ProyectoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProcesarActividadType extends AbstractType
{
    
    public function __construct($proyecto)
    {
        $this->proyecto =  $proyecto;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $proyecto = $this->proyecto;

        $builder
            ->add('descripcion','textarea')
            ->add('tipotiempo','choice',array('choices'=>array(2=>'Horas',3=>'Minutos')))
            ->add('tiempoestimado','text')
            
            //->add('ubicacion')
            
            ->add('responsable','entity',
                array(
                    'class' => 'ProyectoBundle:Miembroproyecto',
                    'empty_value'=>'Seleccione...','label'=>'Responsable',
                    
                    'multiple'=>false,
                    'expanded'=>false,
                    'query_builder' => function(EntityRepository $x) use ($proyecto){
                    return $x->createQueryBuilder('x')
                    ->where("x.proyecto =:proyecto")
                    ->setParameter('proyecto', $proyecto)
                ;}))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ProyectoBundle\Entity\ProcesarActividad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_proyectobundle_procesaractividad';
    }
}
