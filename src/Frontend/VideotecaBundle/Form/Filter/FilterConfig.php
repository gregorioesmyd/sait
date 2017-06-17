<?php

namespace Frontend\VideotecaBundle\Form\Filter;

use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\CintaType;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\CntPrensaType;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\CntVideotecaType;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\CntSateliteType;
use Frontend\VideotecaBundle\Form\Filter\WrapperCinta\CntDeporteType;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\SegmentoType;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\SgtPrensaType;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\SgtVideotecaType;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\SgtSateliteType;
use Frontend\VideotecaBundle\Form\Filter\WrapperSegmento\SgtDeporteType;

/**
* 
*/
class FilterConfig
{
	private $tipoCinta;

	function __construct($tipoCinta) {
		if ($tipoCinta != null) 
		{
			$this->tipoCinta = $tipoCinta;
		}
	}

	public function initForm($builder)
	{
		$nombreTipoCinta = ($this->tipoCinta != null) ? \trim( $this->tipoCinta->getSlug() ) : 'default' ;
		switch ($nombreTipoCinta) {
			case "prensa":
				$builder
					->add('cinta', new CntPrensaType($this->tipoCinta))
					->add('segmento', new SgtPrensaType())
				;
				break;

			case "videoteca":
				$builder
					->add('cinta', new CntVideotecaType($this->tipoCinta))
					->add('segmento', new SgtVideotecaType())
				;
				break;

			case "deporte":
				$builder
					->add('cinta', new CntDeporteType($this->tipoCinta))
					->add('segmento', new SgtDeporteType())
				;
				break;

			case "satelite":
				$builder
					->add('cinta', new CntSateliteType($this->tipoCinta))
					->add('segmento', new SgtSateliteType())
				;
				break;

			default:
				$builder
					->add('cinta', new CintaType())
					->add('segmento', new SegmentoType())
				;
		}

		return $builder;
	}

}
