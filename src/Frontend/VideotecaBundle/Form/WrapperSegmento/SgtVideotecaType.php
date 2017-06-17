<?php

namespace Frontend\VideotecaBundle\Form\WrapperSegmento;

use Frontend\VideotecaBundle\Form\WrapperSegmento\BaseSegmentoType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\WrapperSegmento\Config\VideotecaConfig;

class SgtVideotecaType extends BaseSegmentoType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('tituloOriginal', null, VideotecaConfig::$tituloOriginal)
            ->add('anioRealizado', null, VideotecaConfig::$anioRealizado)
            ->add('productor', null, VideotecaConfig::$productor)
            ->add('director', null, VideotecaConfig::$director)
            ->add('proveedor', null, VideotecaConfig::$proveedor)
            ->add('autoridad', null, VideotecaConfig::$autoridad)
            ->add('fechaIngreso', 'date', VideotecaConfig::$fechaIngreso)
            ->add('evaluador', null, VideotecaConfig::$evaluador)
            ->add('paisProductor', null, VideotecaConfig::$paisProductor)
            ->add('genero', null, VideotecaConfig::$genero)
            ->add('franja', null, VideotecaConfig::$franja)
            ->add('clasificacion', null, VideotecaConfig::$clasificacion)
            ->add('elemento', null, VideotecaConfig::$elemento)
            ->add('tqc', null, VideotecaConfig::$tqc)
            ->add('norma', null, VideotecaConfig::$norma)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperSegmento\SgtVideoteca'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_sgtvideoteca';
    }
}
