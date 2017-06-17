<?php

namespace Frontend\TruequesBundle\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityRepository;

use Frontend\TruequesBundle\Entity\Producto;
use Frontend\TruequesBundle\Entity\Categoria;

class AddProductoFieldSubscriber implements EventSubscriberInterface
{
    private $propertyPathToProducto;

    public function __construct($propertyPathToProducto)
    {
        $this->propertyPathToProducto = $propertyPathToProducto;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addProductoForm($form, $categoria_id)
    {
        $formOptions = array(
            'class'         => 'TruequesBundle:Producto',
            'empty_value'   => 'Seleccione un producto',
            'label'         => 'Producto',
            'required'=>true,
            'attr'          => array(
                'class' => 'producto_selector',
            ),
            'query_builder' => function (EntityRepository $repository) use ($categoria_id) {
                $qb = $repository->createQueryBuilder('producto')
                    ->innerJoin('producto.categoria', 'categoria')
                    ->where('categoria.id = :categoria')
                    ->setParameter('categoria', $categoria_id)
                ;

                return $qb;
            }
        );

        $form->add($this->propertyPathToProducto, 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor    = PropertyAccess::createPropertyAccessor();

        $producto       = $accessor->getValue($data, $this->propertyPathToProducto);
        $categoria_id     = ($producto) ? $producto->getCategoria()->getId() : null;

        $this->addProductoForm($form, $categoria_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $categoria_id = array_key_exists('categoria', $data) ? $data['categoria'] : null;

        if( $categoria_id == '' ){
            $categoria_id = NULL;
        }
        
        $this->addProductoForm($form, $categoria_id);
    }
}

