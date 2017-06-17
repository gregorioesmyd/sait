<?php

namespace Frontend\EncuestaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuariosegundosType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('segundos')
            ->add('idusuario')
            ->add('idencuesta')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EncuestaBundle\Entity\Usuariosegundos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_encuestabundle_usuariosegundos';
    }
}
