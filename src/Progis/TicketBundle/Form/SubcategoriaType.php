<?php

namespace Progis\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SubcategoriaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('subcategoria')
            ->add('tipotiempo','choice',array('choices'=>array(2=>'Horas',3=>'Minutos'),'label'=>'Tipo tiempo'))
            ->add('tiempoatencion','text',array('label'=>'Tiempo de atención'))
            ->add('complejidad',null,array('empty_value'=>'Seleccione...'))
            ->add('urlwiki',null,array('label'=>'Url Wiki'))
    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\TicketBundle\Entity\Subcategoria'
        ));
    }

    public function getName()
    {
        return 'progis_ticketbundle_subcategoriatype';
    }
}
