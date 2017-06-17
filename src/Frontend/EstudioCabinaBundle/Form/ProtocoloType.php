<?php

namespace Frontend\EstudioCabinaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProtocoloType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $refigerios=array('Cafe'=> 'Café','Agua'=> 'Agua','Sin refrigerio'=> 'Sin refrigerio');

        $builder
            ->add('nroPersonas', 'text', array(
                    'attr'  => array(
                    'style' => 'width:60px;'
                   )
                ))
            ->add('refrigerio','choice',array(
                    'choices'=>$refigerios,
                    'multiple' => true,
                    'expanded' => true,
                ))
    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\EstudioCabinaBundle\Entity\Protocolo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_estudiocabinabundle_protocolo';
    }
}
