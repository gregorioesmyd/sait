<?php

namespace Progis\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TicketType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nivelorganizacional','entity',array(
                'class' => 'UsuarioBundle:Nivelorganizacional',
                'multiple'=>false,
                'expanded'=>true,
                'label'=>'Unidad',
                'query_builder' => function(EntityRepository $x){
                return $x->createQueryBuilder('x')
                ->where("x.codigo like '%01-06-02___%'")
                ;}
            ))
            ->add('solicitud','textarea')
            ->add('extension')
            ->add('file',null,array('label'=>'Archivo',"attr" => array(
                "multiple" => "multiple",
                "name" => "files[]",
            )))
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\TicketBundle\Entity\Ticket'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_ticketbundle_ticket';
    }
}
