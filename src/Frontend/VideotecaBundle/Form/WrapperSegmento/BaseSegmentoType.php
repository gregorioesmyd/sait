<?php

namespace Frontend\VideotecaBundle\Form\WrapperSegmento;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\WrapperSegmento\Config\SegmentoConfig;

/**
* 
*/
class BaseSegmentoType extends AbstractType
{
	
    /**
     * @param  FormBuilderInterface $builder
     * @param  array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('titulo', null, SegmentoConfig::$titulo)
                ->add('tituloSerie', null, SegmentoConfig::$tituloSerie)
                ->add('tituloAlterno', null, SegmentoConfig::$tituloAlterno)
                ->add('sinopsis', null, SegmentoConfig::$sinopsis)
                ->add('pais', null, SegmentoConfig::$pais)
                ->add('idioma', null, SegmentoConfig::$idioma)
                ->add('fechaEvento', 'date', SegmentoConfig::$fechaEvento)
                ->add('inicio', 'datetime', SegmentoConfig::$inicio)
                ->add('fin', 'datetime', SegmentoConfig::$fin)
                ->add('duracion', 'datetime', SegmentoConfig::$duracion)
                ->add('observaciones', null, SegmentoConfig::$observaciones);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
       $resolver->setDefaults(array(
           'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento'
       ));
    }

    /**
     * @return string
     */
    public function getName()
    {
            return 'base_cinta';
    }
}
