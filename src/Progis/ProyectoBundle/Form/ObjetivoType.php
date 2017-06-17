<?php

namespace Progis\ProyectoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObjetivoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion','textarea')
            ->add('fechainicioestimada','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha inicio estimada'       ))
            ->add('fechafinestimada','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha fin estimada'))
            ->add('etapa',null,array('empty_value'=>'Seleccione...'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ProyectoBundle\Entity\Objetivo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_proyectobundle_objetivo';
    }
}
