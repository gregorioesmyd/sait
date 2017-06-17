<?php

namespace Progis\PrincipalBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetasType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechadesde','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha desde'       ))
            ->add('fechahasta','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'label'=>'Fecha hasta'       ))
            ->add('observacion')
            ->add('miembroespacio')
            ->add('jornadalaboral')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\PrincipalBundle\Entity\Metas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_principalbundle_metas';
    }
}
