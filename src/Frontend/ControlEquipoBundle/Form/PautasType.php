<?php

namespace Frontend\ControlEquipoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PautasType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaDesde','date', array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',  ))
            ->add('fechaHasta','date', array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',  ))
            //->add('responsable', null, array('empty_value' => 'Seleccione...'))
            ->add('responsable', 'entity', array(
                        'mapped' => true,
                        'class' => 'UsuarioBundle:Perfil',
                        'empty_value' => 'Seleccione...',
                        'query_builder' => function(EntityRepository $e) {
                    return $e->createQueryBuilder('a')
                            ->innerJoin('a.user', 'u')
                            ->where('u.isActive = TRUE')
                            ->orderBy('a.id', 'ASC');
                }
            ))
            ->add('justificacion','textarea')
            ->add('aprobado')
            ->add('tipoPauta','hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ControlEquipoBundle\Entity\Pautas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'controlequipobundle_pautas';
    }
}
