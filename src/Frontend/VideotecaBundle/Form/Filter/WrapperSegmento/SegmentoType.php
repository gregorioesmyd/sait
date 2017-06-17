<?php

namespace Frontend\VideotecaBundle\Form\Filter\WrapperSegmento;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\Config\SegmentoConfig;

class SegmentoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', null, SegmentoConfig::$titulo)
            ->add('tituloSerie', null, SegmentoConfig::$tituloSerie)
            ->add('tituloAlterno', null, SegmentoConfig::$tituloAlterno)
            ->add('sinopsis', null, SegmentoConfig::$sinopsis)
            ->add('pais', 'entity', array(
                'class' => 'VideotecaBundle:Paises',
                'label' => 'Pais:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                ),
                'empty_value' => 'Elija el pais'
            ))
            ->add('idioma', 'entity', array(
                'class' => 'VideotecaBundle:Idioma',
                'label' => 'Idioma:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                ),
                'empty_value' => 'Elija el idioma'
            ))
            ->add('fechaEvento', 'text', SegmentoConfig::$fechaEvento)
            ->add('inicio', 'datetime', SegmentoConfig::$inicio)
            ->add('fin', 'datetime', SegmentoConfig::$fin)
            ->add('duracion', 'datetime', SegmentoConfig::$duracion)
            ->add('observaciones', null, SegmentoConfig::$observaciones)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_segmento';
    }
}
