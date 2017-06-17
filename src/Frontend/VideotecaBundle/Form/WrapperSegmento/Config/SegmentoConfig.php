<?php

namespace Frontend\VideotecaBundle\Form\WrapperSegmento\Config;

/**
*
*/
class SegmentoConfig
{

	public static $inicio = array(
        'label' => 'Inicio:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control col-xs-4 timepicker'
        ),
        'widget' => 'single_text',
        'format' => 'HH:mm:ss',
    );

    public static $fin = array(
        'label' => 'Fin:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control col-xs-4 timepicker'
        ),
        'widget' => 'single_text',
        'format' => 'HH:mm:ss',
    );

    public static $duracion = array(
        'label' => 'Duración:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control col-xs-4 timepicker'
        ),
        'widget' => 'single_text',
        'format' => 'HH:mm:ss',
    );

    public static $fechaEvento = array(
        'label' => 'Fecha Evento:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control col-xs-4 datepicker'
        ),
        'widget' => 'single_text',
        'format'   => 'dd-MM-yyyy',
    );

    public static $titulo = array(
        'label' => 'Titulo:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        )
    );

    public static $tituloSerie = array(
        'label' => 'Titulo Serie:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        )
    );

    public static $sinopsis = array(
        'label' => 'Sinopsis:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        )
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

    public static $tituloAlterno = array(
        'label' => 'Titulo Alterno:',
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
        'empty_value' => 'Elija la Propiedad'
    );

    public static $pais = array(
        'label' => 'Pais:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el pais'
    );

    public static $idioma = array(
        'label' => 'Idioma:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el idioma'
    );
}
