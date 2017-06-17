<?php

namespace Progis\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class informeGestionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

                ->add('anio','choice',array(
                    'choices'   => array(\date("Y")-2=>\date("Y")-2,\date("Y")-1=>\date("Y")-1,\date("Y")=>\date("Y")),
                    'multiple'=>false,
                    'expanded'=>false,
                ))
                ->add('mes','choice',array(
                    'choices'   => array("01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio","07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre"),
                    'multiple'=>false,
                    'expanded'=>false,
                ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progis\ReporteBundle\Entity\informeGestion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progis_reportebundle_informegestion';
    }
}
