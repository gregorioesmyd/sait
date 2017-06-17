<?php

namespace Frontend\EstudioCabinaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PautafijasType extends AbstractType
{
    public function __construct($tipo)
    {
        $this->tipo = $tipo;

    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tipo = $this->tipo;
        $minute = array(0,30);
       
        $builder
                
            ->add('diasPautas', 'choice',array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices'  => array(1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles',4 => 'Jueves', 5 => 'Viernes',6 => 'Sábado', 7 => 'Domingo'),
            ))
                
            ->add('pauta')
                
            ->add('horainicio', 'time', array(
                    'empty_value' => 'Seleccione...',
                    'input'         => 'datetime',
                    'widget'        => 'choice',
                    'minutes'       => array_combine($minute, $minute),
                ))
             
            ->add('horafin', 'time', array(
                    'empty_value' => 'Seleccione...',
                    'input'         => 'datetime',
                    'widget'        => 'choice',
                    'minutes'       => array_combine($minute, $minute),
                ))
             ->add('postProductor', 'entity', array(
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
            ->add('observacion','textarea')
            ->add('estudioCabina', null, array('empty_value' => 'Seleccione...'))
            ->add('estudioCabina', 'entity', array(
                        'mapped' => true,
                        'class' => 'EstudioCabinaBundle:EstudiosCabinas',
                        'empty_value' => 'Seleccione...',
                        'query_builder' => function(EntityRepository $e)use ($tipo) {
                    return $e->createQueryBuilder('a')
                        ->where('a.tipo = :tipo')
                        ->setParameter('tipo',$tipo);
                }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EstudioCabinaBundle\Entity\Pautafijas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_estudiocabinabundle_pautafijas';
    }
}
