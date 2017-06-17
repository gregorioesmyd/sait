<?php

namespace Progis\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class estadoProcesoType extends AbstractType
{
    public function __construct($idNivel)
    {
        $this->idNivel =  $idNivel;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idNivel=$this->idNivel;
        $arrayEstadoProceso=array(1=>"Nuevo",2=>"Proceso",3=>"Revisión",5=>"Dependencia");
        $builder
            ->add('miembroespacio','entity',
                array(
                    'class' => 'PrincipalBundle:Miembroespacio',
                    'multiple'=>true,
                    'expanded'=>false,
                    'query_builder' => function(EntityRepository $x) use ($idNivel){
                    return $x->createQueryBuilder('x')
                    ->where("x.nivelorganizacional in (:id)")
                    ->setParameter('id', $idNivel)
                ;}))
                ->add('tipo','choice',array(
                    'choices'   => array("t"=>"Ticket","p"=>"Proyecto"),
                    'multiple'=>true,
                    'expanded'=>false,
                ))
                ->add('estatusProceso','choice',array(
                    'choices'   => $arrayEstadoProceso,
                    'multiple'=>true,
                    'expanded'=>false,
                ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ReporteBundle\Entity\estadoProceso'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_reportebundle_estadoproceso';
    }
}
