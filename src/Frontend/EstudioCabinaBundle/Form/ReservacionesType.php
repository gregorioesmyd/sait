<?php

namespace Frontend\EstudioCabinaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ReservacionesType extends AbstractType
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
            ->add('pauta','textarea')
            ->add('fecha','date',array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-y',
               ))
            
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
                 
            ->add('observacion','textarea')                    
            ->add('estudioCabina', 'entity', array(
                        'mapped' => true,
                        'class' => 'EstudioCabinaBundle:EstudiosCabinas',
                        'empty_value' => 'Seleccione...',
                       # 'data'=>$tipo,
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
            'data_class' => 'Frontend\EstudioCabinaBundle\Entity\Reservaciones'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_estudiocabinabundle_reservaciones';
    }
}
