<?php

namespace Frontend\TruequesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Frontend\TruequesBundle\Form\AddCategoriaFieldSubscriber;
use Frontend\TruequesBundle\Form\AddProductoFieldSubscriber;

class MisProductosType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $propertyPathToProducto = 'productoCambiar';
        
        $builder
                ->addEventSubscriber(new AddCategoriaFieldSubscriber($propertyPathToProducto))
            ->addEventSubscriber(new AddProductoFieldSubscriber($propertyPathToProducto))
            
        ;
        

        $builder
            // ->add('fechaVencimiento', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-y', 'attr'=>array('placeholder'=>'Fecha de vencimiento')))
            ->add('mis_producto_interes',null,array('empty_value' => 'Seleccione un producto de interes',))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\TruequesBundle\Entity\MisProductos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_truequesbundle_misproductos';
    }
}
