<?php

namespace Frontend\VideotecaBundle\Form\WrapperSegmento;

use Frontend\VideotecaBundle\Form\WrapperSegmento\BaseSegmentoType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\WrapperSegmento\Config\DeporteConfig;

class SgtDeporteType extends BaseSegmentoType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('disciplina', null, DeporteConfig::$disciplina)
            ->add('corresponsal', null, DeporteConfig::$corresponsal)
            ->add('nroREM', null, DeporteConfig::$nroREM)
            ->add('nroHistoria', null, DeporteConfig::$nroHistoria)
            ->add('nroNota', null, DeporteConfig::$nroNota)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperSegmento\SgtDeporte'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_sgtdeporte';
    }
}
