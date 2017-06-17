<?php

namespace Frontend\VideotecaBundle\Form\WrapperCinta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Frontend\VideotecaBundle\Form\WrapperCinta\Config\CintaConfig;

use Doctrine\ORM\EntityRepository;

/**
 *
 */
class CintaType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('tipoCinta', null, CintaConfig::$tipoCinta)
            //->add('codigo', null, CintaConfig::$codigo)
            ->add('formato', null, CintaConfig::$formato)
            ->add('duracion', null, CintaConfig::$duracion)
            ->add('evento', null, CintaConfig::$evento)
            ->add('marca', null, CintaConfig::$marca)
            ->add('observaciones', null, CintaConfig::$observaciones)
            ->add('documentalista', null, CintaConfig::$documentalista)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_videotecabundle_cinta';
    }
}
