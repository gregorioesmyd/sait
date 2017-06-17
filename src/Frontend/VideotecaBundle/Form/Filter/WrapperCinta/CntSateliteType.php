<?php

namespace Frontend\VideotecaBundle\Form\Filter\WrapperCinta;

use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\BaseCintaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\Config\CintaConfig;

use Doctrine\ORM\EntityRepository;

class CntSateliteType extends BaseCintaType
{
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
	    $tipoCinta = $this->tipoCinta;
        $builder->add('categoria', null, array(
            'class' => 'VideotecaBundle:Categoria',
            'label' => 'Categoria:',
            'label_attr' => array(
                'class' => 'col-xs-3 control-label'
            ),
            'attr' => array(
                'class' => 'form-control'
            ),
            'query_builder' => function (EntityRepository $repository) use ($tipoCinta){
                $qb = $repository->createQueryBuilder('categoria')
                    ->where('categoria.tipoCinta = :tipoCinta')
                    ->setParameter('tipoCinta',  $tipoCinta)
                ;
                return $qb;
            },
			'empty_value' => 'Elija la Categoria'
        ));
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
        return 'frontend_videotecabundle_cntsatelite';
    }
}
