<?php

namespace Frontend\ContratosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class ContratosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaRegistro','date',array(
                    'widget' => 'single_text',
                    'format' => 'yy-MM-dd',
                )) 
            ->add('codigo')
            ->add('empresa','textarea')
            ->add('fechaInicio','date',array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-y',
                )) 
            ->add('fechaFin','date',array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-y',
                )) 
            ->add('duracion')
            ->add('montoBs')
            ->add('montoDolares')
            ->add('montoEuros')
            ->add('obra','textarea')
            ->add('idDireccion')
            ->add('idAbogado','entity',array(
                'class' => 'ContratosBundle:Abogados',
                'multiple'=>false,
                'expanded'=>false,
                'label'=>'Abogado',
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->where("x.estatus is null")
                ;}
            ))               
            ->add('file','file')
            ->add('archivo')
            ->add('personal')
            ->add('inactivo')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ContratosBundle\Entity\Contratos'
        ));
    }

    public function getName()
    {
        return 'frontend_contratosbundle_contratostype';
    }
}
