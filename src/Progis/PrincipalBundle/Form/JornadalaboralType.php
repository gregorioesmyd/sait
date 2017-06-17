<?php

namespace Progis\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JornadalaboralType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dias','choice',array(
                        'choices'=>array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'),
                        'expanded'=>true, 
                        'multiple'=>true,
                        ))
            ->add('horadesde',null,array('empty_value'=>'Seleccione...','attr' => array('class'=>'horas')))
            ->add('horahasta',null,array('empty_value'=>'Seleccione...','attr' => array('class'=>'horas')))
            ->add('horasDiarias','text')
            ->add('observacion','textarea')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\PrincipalBundle\Entity\Jornadalaboral'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_principalbundle_jornadalaboral';
    }
}
