<?php

namespace Frontend\CumpleaniosBundle\Form\Efemerides;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EfemeridesType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $days = NULL;
        for ($i=1; $i <= 31; $i++) { 
            $days[$i] = $i;
        }

        $builder
                ->add('nombre')
                ->add('anio', null, array(
                    'required' => false
                        )
                )
                ->add('mes', 'choice', array(
                    'choices' => array(
                        "01" => "Enero",
                        "02" => "Febrero",
                        "03" => "Marzo",
                        "04" => "Abril",
                        "05" => "Mayo",
                        "06" => "Junio",
                        "07" => "Julio",
                        "08" => "Agosto",
                        "09" => "Septiembre",
                        "10" => "Octubre",
                        "11" => "Noviembre",
                        "12" => "Diciembre"
                    ),
                    'empty_value' => 'Elija una opción',
                    'empty_data' => null
                ))
                ->add('dia', 'choice', array(
                    'choices' => $days,
                    'empty_value' => 'Elija una opción',
                    'empty_data' => null
                ))
                ->add('tipoId')
                ->add('paisId')
                ->add("isAnio", "checkbox", array(
                    'mapped' => false
                        )
                )
        ;
        
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\CumpleaniosBundle\Entity\Efemerides\Efemerides'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'frontend_cumpleaniosbundle_efemerides_efemerides';
    }

}
