<?php

namespace Frontend\ControlEquipoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class EquiposInternosType extends AbstractType
{
    
    public function __construct($marca) {
        $this->marca = $marca;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $marca = $this->marca;
        $builder
            ->add('fechaOrdenCompra','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',  ))
            ->add('codigoCatalogo')
            ->add('codigoBien')
            ->add('descripcionEquipo','textarea')
            ->add('codigoBarra')
            ->add('serial')
            ->add('responsablePatrimonial',null,array('empty_value' => 'Seleccione...'))
             ->add('responsableUso',null,array('empty_value' => 'Seleccione...'))
            ->add('controlPerceptivo','date',array(
                    'widget' => 'single_text',
                    'format'   => 'dd-MM-y',  ))
            ->add('ordenCompra','text')
            ->add('solicitudContratacion')
            ->add('partidaPresupuestaria')
            ->add('nroFactura')
            ->add('moneda')
            ->add('precio','text')
            ->add('actaEntrega')
            ->add('file','file')
            ->add('fotoReferencia')
            ->add('marca', 'entity', array(
                        'mapped' => true,
                        'class' => 'ControlEquipoBundle:Marcas',
                        'empty_value' => 'Seleccione...',
                        'query_builder' => function(EntityRepository $e)  {
                            return $e->createQueryBuilder('a')
                                     ->orderBy('a.descripcionMarca', 'ASC');
                        }
            ))
            ->add('anio','text')  
            ->add('color')  
            ->add('numeroPiezas','text')            
            ->add('observaciones','textarea')
            ->add('moneda')
            ->add('modelo', 'entity', array(
                        'mapped' => true,
                        'class' => 'ControlEquipoBundle:Modelos',
                        'empty_value' => 'Seleccione...',
                        'query_builder' => function(EntityRepository $e) use ($marca) {
                    return $e->createQueryBuilder('a')
                        ->where('a.marca = :marca')
                        ->setParameter('marca', $marca);
                        //->orderBy('a.id', 'ASC');
                }
            ))
            ->add('proveedor',null,array( 'empty_value' => 'Seleccione...'))
            ->add('ubicacionFisica',null,array( 'empty_value' => 'Seleccione...'))
            ->add('ubicacionAdministrativa',null,array( 'empty_value' => 'Seleccione...'))
                    
            ->add('valorInicial','text')
            ->add('depreciacionMensual','text')
            ->add('depreciacionAcumulada','text')
            ->add('valorActual','text')
                    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\ControlEquipoBundle\Entity\EquiposInternos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'controlequipobundle_equipointerno';
    }
}
