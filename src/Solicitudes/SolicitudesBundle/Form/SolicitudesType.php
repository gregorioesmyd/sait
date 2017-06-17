<?php

namespace Solicitudes\SolicitudesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Solicitudes\SolicitudesBundle\Entity\Requerimientos;

class SolicitudesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('problema', 'textarea')
            ->add('nombre')
            ->add('beneficiarios', 'textarea')
            ->add('solicitante')
            ->add('estatus')
            ->add('requerimientos', 'collection', array(
               'type'           => new RequerimientosType(),
               'label'          => 'Requerimientos',
               'by_reference'   => false,
               'allow_delete'   => true,
               'allow_add'      => true,
               'attr'           => array(
                   'class' => 'row'
               )
           ))
            ->add('responsables', 'collection', array(
               'type'           => new ResponsablesType(),
               'label'          => 'Responsables',
               'by_reference'   => false,
               'allow_delete'   => true,
               'allow_add'      => true,
               'attr'           => array(
                   'class' => 'row'
               )
           ))           

            ->add('unidades', 'collection', array(
               'type'           => new UnidadesType(),
               'label'          => 'Unidades',
               'by_reference'   => false,
               'allow_delete'   => true,
               'allow_add'      => true,
               'attr'           => array(
                   'class' => 'row'
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
            'data_class' => 'Solicitudes\SolicitudesBundle\Entity\Solicitudes'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'solicitudes_solicitudesbundle_solicitudes';
    }
}
