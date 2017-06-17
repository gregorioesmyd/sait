<?php

namespace Frontend\EncuestaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PreguntaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idencuesta')
            ->add('tipopregunta')
            ->add('pregunta', 'textarea')
            ->add('tiempopregunta', 'textarea')
            ->add('file','file')
            ->add('archivo')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EncuestaBundle\Entity\Pregunta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_encuestabundle_pregunta';
    }
}
