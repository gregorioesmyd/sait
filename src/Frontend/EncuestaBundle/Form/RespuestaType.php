<?php

namespace Frontend\EncuestaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RespuestaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('respuesta','textarea')
            ->add('idpregunta')
            ->add('correcta','choice',array('choices'=>array(FALSE=>'Incorrecta',TRUE=>'Correcta')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EncuestaBundle\Entity\Respuesta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_encuestabundle_respuesta';
    }
}
