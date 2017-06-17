<?php

namespace Frontend\VideotecaBundle\Form\WrapperCinta;

use Frontend\VideotecaBundle\Form\WrapperCinta\BaseCintaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class CntPrensaType extends BaseCintaType
{
    /**
     *
     */
    function __construct($tipoCinta) { 
        $this->tipoCinta = $tipoCinta;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta',
            'tipoCinta'  => $this->tipoCinta
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_cntprensa';
    }
}
