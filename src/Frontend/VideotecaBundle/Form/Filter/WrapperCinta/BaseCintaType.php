<?php

namespace Frontend\VideotecaBundle\Form\Filter\WrapperCinta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\Config\CintaConfig;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class BaseCintaType extends AbstractType {

    /**
     * @param  FormBuilderInterface $builder
     * @param  array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $tipoCinta = $options['tipoCinta'];
        $builder->add('tipoCinta', null, CintaConfig::$tipoCinta)
                ->add('codigo', null, CintaConfig::$codigo)
                ->add('formato', 'entity', array(
                    'class' => 'VideotecaBundle:Formato',
                    'label' => 'Formato:',
                    'label_attr' => array(
                        'class' => 'col-xs-3 control-label'
                    ),
                    'attr' => array(
                        'class' => 'form-control'
                    ),
                    'query_builder' => function (EntityRepository $repository) use ($tipoCinta) {
                $qb = $repository->createQueryBuilder('formato')
                        ->leftJoin('formato.ultimoCodigos', 'uc')
                        ->leftjoin('uc.categoria', 'c')
                        ->leftJoin('c.tipoCinta', 't')
                        ->where('t.id = :tipoCinta')
                        ->orderBy('formato.descripcionFormato', 'ASC')
                        ->setParameter('tipoCinta', $tipoCinta)
                        ->distinct()
                ;

                return $qb;
            },
                    'empty_value' => 'Elija el formato'
                ))
                ->add('duracion', null, CintaConfig::$duracion)
                ->add('evento', null, CintaConfig::$evento)
                ->add('marca', null, CintaConfig::$marca)
                ->add('observaciones', null, CintaConfig::$observaciones);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\VideotecaBundle\Entity\wrapperCinta\Cinta'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'base_cinta';
    }

}
