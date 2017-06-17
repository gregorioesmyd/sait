<?php

namespace Progis\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ComentarioarchivoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comentario', 'textarea')
            ->add('tipo' ,'choice',array(
                        'choices'=>array(1=>'Normal',2=>'Por revisión',3=>'Por solución',4=>'Por dependencia')))
            ->add('file',null,array('label'=>'Archivo'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\PrincipalBundle\Entity\Comentarioarchivo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_principalbundle_comentarioarchivo';
    }
}
