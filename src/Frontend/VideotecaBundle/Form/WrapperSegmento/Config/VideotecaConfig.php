<?php 

namespace Frontend\VideotecaBundle\Form\WrapperSegmento\Config;

/**
* 
*/
class VideotecaConfig
{
	
	public static $tituloOriginal = array(
        'label' => 'Titulo Original:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
    );

    public static $anioRealizado = array(
        'label' => 'Año Realizado:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
    );

    public static $productor = array(
        'label' => 'Productor:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
    );

    public static $director = array(
        'label' => 'Director:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
    );

    public static $proveedor = array(
        'label' => 'Proveedor:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        )
    );

    public static $autoridad = array(
        'label' => 'Autoridad:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija la autoridad'
    );

    public static $fechaIngreso = array(
       'label' => 'Fecha Ingreso:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control col-xs-4 datepicker'
        ),
        'widget' => 'single_text',
        'format'   => 'dd-MM-yyyy',
    );

    public static $evaluador = array(
        'label' => 'Evaluador:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        )
    );

    public static $paisProductor = array(
        'label' => 'Pais Productor:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el pais'
    );

    public static $genero = array(
        'label' => 'Genero:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el genero'
    );

    public static $franja = array(
        'label' => 'Franja:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija la franja'
    );

    public static $clasificacion = array(
        'label' => 'Clasificacion:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija la clasificacion'
    );

    public static $elemento = array(
        'label' => 'Elemento:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el elemento'
    );

    public static $tqc = array(
        'label' => 'TQC:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija el TQC'
    );

    public static $norma = array(
        'label' => 'Norma:',
        'label_attr' => array(
            'class' => 'col-xs-3 control-label'
        ),
        'attr' => array(
            'class' => 'form-control'
        ),
        'empty_value' => 'Elija la norma'
    );


}
