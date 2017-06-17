<?php

namespace Frontend\EncuestaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EncuestaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minute = array(0,30);
        $builder
            ->add('nombre')
            ->add('tipoencuesta')
            ->add('fechainicioencuesta','date',array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-y',
                ))
            ->add('horainicio', 'time', array(
                    'empty_value' => 'Seleccione...',
                    'input'         => 'datetime',
                    'widget'        => 'choice',
                ))

            ->add('horafin', 'time', array(
                    'empty_value' => 'Seleccione...',
                    'input'         => 'datetime',
                    'widget'        => 'choice',
                ))
            ->add('fechafinencuesta','date',array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-y',
                ))
            ->add('tema','textarea')
            ->add('numeropregunta','textarea')
            ->add('puntospregunta','textarea')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EncuestaBundle\Entity\Encuesta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_encuestabundle_encuesta';
    }
}
