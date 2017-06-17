<?php

namespace Frontend\VideotecaBundle\Form\WrapperCinta\Config;

use Doctrine\ORM\EntityRepository;

/**
*
*/
class CintaConfig
{

    public static $codigo = array(
        'label' => 'Código:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        );

    public static $formato = array(
        'label' => 'Formato:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija el Formato'
        );

    public static $duracion = array(
        'label' => 'Duración:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija la Duración'
        );

    public static $tipoCinta = array(
        'label' => 'Tipo de Cinta:',
        'label_attr' => array(
            'class' => 'col-xs-8 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija el Tipo Cinta'
        );

    public static $evento = array(
        'label' => 'Evento:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija el Evento'
        );

    public static $marca = array(
        'label' => 'Marca:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija la Marca'
        );

    public static $observaciones = array(
        'label' => 'Observaciones:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            )
        );

    public static $propiedad = array(
        'label' => 'Propiedad:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'Elija la propiedad'
        );

    public static $documentalista = array(
        'label' => 'Documentalista:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
            ),
        'attr' => array(
            'class' => 'form-control'
            ),
        'empty_value' => 'ELija el documentalista'
    );

    // public static $categoria = array(
    //     'label' => 'Categoria:',
    //     'label_attr' => array(
    //         'class' => 'col-xs-3 control-label'
    //         ),
    //     'attr' => array(
    //         'class' => 'form-control'
    //         )
    //     );

    // public static function optionsCategoria()
    // {
    //     static $categoria = array(
    //         'class' => 'VideotecaBundle:Categoria',
    //         'query_builder' => function(EntityRepository $er) {
    //             return $er->createQueryBuilder('c')
    //                 ->where('c.tipoCinta = :tipoCinta')
    //                 ->setParameter('tipoCinta', 4);
    //         },
    //         'label' => 'Categoria:',
    //         'label_attr' => array(
    //             'class' => 'col-xs-3 control-label'
    //         ),
    //         'attr' => array(
    //             'class' => 'form-control'
    //         )
    //     );

    //     return self::$categoria;
    // }
}
