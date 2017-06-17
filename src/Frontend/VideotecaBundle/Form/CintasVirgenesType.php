<?php

namespace Frontend\VideotecaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CintasVirgenesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreCv', null, array(
                'label' => 'Nombre CV:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('existencia', null, array(
                'label' => 'Existencia:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('idFormato', null, array(
                'empty_value' => 'Seleccione...',
                'label' => 'Formato:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\CintasVirgenes'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_cintasvirgenes';
    }
}
