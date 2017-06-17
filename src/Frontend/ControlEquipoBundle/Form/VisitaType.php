<?php

namespace Frontend\ControlEquipoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VisitaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('fechaentrada')
            ->add('horaentrada')
            ->add('fechasalida')
            ->add('horasalida')*/
            ->add('contacto','textarea')
            ->add('observaciones','textarea')
            //->add('estatus')
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ControlEquipoBundle\Entity\Visita',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_controlequipobundle_visita';
    }
}
