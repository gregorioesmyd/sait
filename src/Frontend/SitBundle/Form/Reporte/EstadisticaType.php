<?php

namespace Frontend\SitBundle\Form\Reporte;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EstadisticaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function __construct($dato)
    {
        $this->dato = $dato;

    }

    public function buildForm(FormBuilderInterface $builder, array $options){

        $dato = $this->dato;

        $builder->add('fechadesde', 'date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'read_only' => true));
        $builder->add('fechahasta', 'date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',
                    'read_only' => true));
        $builder->add('usuario','choice',array('choices'   => $dato,'multiple'=>true));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\SitBundle\Entity\Reporte\Estadistica'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reporte_estadistica';
    }
}
