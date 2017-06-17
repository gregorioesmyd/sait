<?php

namespace Frontend\EstudioCabinaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HistorialReservacionesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario')
            ->add('fechaHora')
            ->add('estatus')
            ->add('estatus2')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EstudioCabinaBundle\Entity\HistorialReservaciones'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_estudiocabinabundle_historialreservaciones';
    }
}
