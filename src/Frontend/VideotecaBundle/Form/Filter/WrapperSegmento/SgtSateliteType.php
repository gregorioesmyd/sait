<?php

namespace Frontend\VideotecaBundle\Form\Filter\WrapperSegmento;

use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\BaseSegmentoType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\Config\SateliteConfig;

class SgtSateliteType extends BaseSegmentoType
{    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('corresponsal', null, SateliteConfig::$corresponsal)
            ->add('nroREM', null, SateliteConfig::$nroREM)
            ->add('nroHistoria', null, SateliteConfig::$nroHistoria)
            ->add('nroNota', null, SateliteConfig::$nroNota)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperSegmento\SgtSatelite'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_sgtsatelite';
    }
}
