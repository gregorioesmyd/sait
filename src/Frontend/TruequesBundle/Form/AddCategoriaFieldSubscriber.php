<?php

namespace Frontend\TruequesBundle\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

use Frontend\TruequesBundle\Entity\Producto;
use Frontend\TruequesBundle\Entity\Categoria;

class AddCategoriaFieldSubscriber implements EventSubscriberInterface
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

    private function addCategoriaForm($form, $categoria = null)
    {
        $formOptions = array(
            'class'         => 'TruequesBundle:Categoria',
            'mapped'        => false,
            'label'         => 'Categoria',
            'empty_value'   => 'Seleccione una categoria',
            'required'=>true,
            'attr'          => array(
                'class' => 'categoria_selector',
            ),
        );

        if ($categoria) {
            $formOptions['data'] = $categoria;
        }

        $form->add('categoria', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::getPropertyAccessor();

        $producto    = $accessor->getValue($data, $this->propertyPathToProducto);
        $categoria = ($producto) ? $producto->getCategoria() : null;

        $this->addCategoriaForm($form, $categoria);
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        $this->addCategoriaForm($form);
    }
}