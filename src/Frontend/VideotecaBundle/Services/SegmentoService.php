<?php

namespace Frontend\VideotecaBundle\Services;

/**
 * Description of SegmentoService
 *
 * @author ecastro
 */
class SegmentoService {

	private $em;
	private $query;
	private $qb;
	private $tipoCinta;
	private $eSegmento;
	private $inputs;
	private $inputsActive;
	private $session;

	public function __construct($em,$session) {
		$this->em = $em;
		$this->session = $session;
	}

	public function pagination($paginator, $page, $query) {
		$pagination = $paginator->paginate(
			$query, $page, 12
			);
		return $pagination;
	}

	public function getQuery($method, $inputs, $tipoCinta) {
		$this->inputs = $inputs;

		if (!$tipoCinta) {
			$this->eSegmento = 'Segmento';
		}

		if (\array_key_exists('cinta', $this->inputs)) {
			if (!empty($inputs['cinta']['tipoCinta'])) {
				$this->getEntitySegmento();
			}
		}
		
		$pathEntity = 'VideotecaBundle:WrapperSegmento\\' . $this->eSegmento;
		$this->qb = $this->em->createQueryBuilder();
		$this->qb->select('s')
		->from("$pathEntity", 's')
		->leftJoin('s.cinta', 'c')
		->leftJoin('s.pais', 'pa')
		->leftJoin('s.idioma', 'id')
		->leftJoin('s.documentalista', 'docum')
		->leftJoin('c.formato', 'f')
		->leftJoin('c.duracion', 'dur')
		->leftJoin('c.evento', 'ev')
		->leftJoin('c.marca', 'm')
		->leftJoin('c.tipoCinta', 't')
		->leftJoin('c.status', 'st')
		->leftJoin('c.categoria', 'cat')
		->leftJoin('c.propiedad', 'prop')
		->orderBy('s.id', 'DESC')
		;

		$this->addJoin();

		if ($method == 'POST') {
			$this->query = $this->addWhere();
		} else {
			$this->query = $this->qb->getQuery();
		}

		return $this->query;
	}

	public function getEntitySegmento() {
		$this->tipoCinta = $this->em->getRepository('VideotecaBundle:TipoCinta')
		->find($this->inputs['cinta']['tipoCinta']);
		
		switch ($this->tipoCinta->getSlug()) {
			case "prensa":
			$this->eSegmento = 'SgtPrensa';
			break;

			case "videoteca":
			$this->eSegmento = 'SgtVideoteca';
			break;

			case "deporte":
			$this->eSegmento = 'SgtDeporte';
			break;

			case "satelite":
			$this->eSegmento = 'SgtSatelite';
			break;
		}
	}

	public function addJoin() {
		if (\array_key_exists('cinta', $this->inputs)) {
			if (!empty($inputs['cinta']['tipoCinta'])) {
				if ($this->tipoCinta->getSlug() == "deporte") {
					$this->qb->innerJoin('s.disciplina', 'disc');
				}

				if ($this->tipoCinta->getSlug() == "videoteca") {
					$this->qb->leftJoin('s.autoridad', 'auto');
					$this->qb->leftJoin('s.genero', 'gen');
					$this->qb->leftJoin('s.franja', 'fr');
					$this->qb->leftJoin('s.clasificacion', 'clas');
					$this->qb->leftJoin('s.elemento', 'elem');
					$this->qb->leftJoin('s.tqc', 'tqc');
					$this->qb->leftJoin('s.norma', 'nor');
				}
			}
		}
	}

	public function addWhere() {
		$this->addWhereDinamic();

	  	// print_r($this->qb->getDQL());
		return $this->qb->getQuery();
	}

	public function addWhereDinamic() {
		foreach ($this->inputs as $key => $aElem) {
			if (\is_array($aElem)) {
				$this->identArray = $key;
				$this->addWhereForm($aElem);
			}
		}
	}

	public function addWhereForm($aElem) {
		foreach ($aElem as $index => $value) {

			if (!empty($value)) {
				$this->inputsActive["$this->identArray"]["$index"] = $value;
				$nameCol = \substr($this->identArray, 0, 1) . '.' . $index;

				if($nameCol=='s.sinopsis'){
					$this->addIn($nameCol, $index, $value);
				}
				if ($nameCol=='s.titulo' || $nameCol=='s.tituloAlterno' || $nameCol=='s.tituloSerie') {
					$this->addLike($nameCol, $index, $value);
				}
				if ($nameCol == 's.fechaEvento') {

					if(strlen($value)=="10" and \strpos($value, '-') || \strpos($value, '/')){
						$this->addEq($nameCol, $index, $value);
					}else{
						$this->validarFecha($nameCol,$value); 
					}
				}

				if ($nameCol != 's.sinopsis' && $nameCol != 's.tituloSerie' && $nameCol != 's.tituloAlterno' && $nameCol != 's.titulo' && $nameCol != 's.fechaEvento') {
					if (\is_numeric($value)) {
						$this->addEq($nameCol, $index, $value);
					}
					else {
						if (\strpos($value, ':')) {
							$this->addEq($nameCol, $index, $value);
						} else {

							$this->addLike($nameCol, $index, $value);
						}
					}
				}
				
			}
		}
	}

	public function addIn($col, $index, $value) {

		$arraySinpsis=explode(",", $value );

		$orX = $this->qb->expr()->orX();

		foreach ($arraySinpsis as $key => $condition) {
			$id = $index . $key;
			$orX->add($this->qb->expr()->like($this->qb->expr()->lower("$col"), ":$id"));
			$this->qb->setParameter("$id", "%". strtolower(trim($condition)) ."%"); 
		}
		$this->qb->andWhere($orX);

	}

	 public function validarFecha($col,$value)
	{
		if(strlen($value)=="4"){
			$date_from = new \Datetime($value.'-01-01');
			$date_to = new \Datetime($value.'-12-31');
			$this->addBetweenAno($col,$date_from,$date_to); 
		}

		if(strlen($value)=="7"){

			$fechaValidar = str_replace("/", "-", $value."-01");
			list($fano, $fmes,$fdias) = explode("-",$fechaValidar); 

			$mes30        = array("04","06","09","11");
			$mes31        = array("01","03","05","07","08","10","12");
			
			if (in_array($fmes, $mes30 ) || in_array($fmes, $mes31 )) {

				$fecha = new \Datetime($fechaValidar);
		        $fechainvertida = date_format($fecha, 'Y-m');
		        $desde = $fechainvertida."-01";
		        $value =$fechainvertida;

		        if (in_array($fmes, $mes30)) {
		          	$hasta =$value."-30";
		        }elseif (in_array($fmes, $mes31)) {
		          	$hasta =$value."-31";
		        }else {
			          if (($fano%4==0 and $fano%100!=0) or $fano%400==0){
			           	 $hasta =$value."-29";
			          } else { 
			           	 $hasta =$value."-28";
			          }
		        }
			        $date_from = new \Datetime($desde);
			        $date_to = new \Datetime($hasta);
					$this->addBetweenMes($col,$date_from,$date_to); 

			}else{

				 $nuevaFecha=str_replace("/", "-", $fmes.'-'.$fano);
				 list($ano,$mes) = explode("-",$nuevaFecha); 

				if (in_array($mes, $mes30 ) || in_array($mes, $mes31 )) {

						$fecha = new \Datetime($nuevaFecha);
						$fechainvertida = date_format($fecha, 'Y-m');
						$desde = $fechainvertida."-01";
						$value =$fechainvertida;
						
						if (in_array($fmes, $mes30)) {
							$hasta =$value."-30";
						}elseif (in_array($fmes, $mes31)) {
							$hasta =$value."-31";
						}else {
							if (($fano%4==0 and $fano%100!=0) or $fano%400==0){
								$hasta =$value."-29";
							} else { 
								$hasta =$value."-28";
							}
						}
							$date_from = new \Datetime($desde);
							$date_to = new \Datetime($hasta);
							$this->addBetweenMes($col,$date_from,$date_to); 
					}else{
						$this->session->getFlashBag()->add('alert', 'Debe indicar una fecha valida para la busqueda, No se encontro resultado..');
					}
			}
		}

		if (strlen($value)=="2") {
		  $this->session->getFlashBag()->add('alert', 'Debe indicar una fecha valida para la busqueda, No se encontro resultado..');
		}
		
	}

	public function addBetweenAno($col,$date_from,$date_to)
	{
			$this->qb->andWhere($this->qb->expr()->between("$col", ":date_from", ":date_to"));
			$this->qb->setParameter('date_from', $date_from);
			$this->qb->setParameter('date_to', $date_to);
	}

	public function addBetweenMes($col,$date_from,$date_to) {
			
			$this->qb->andWhere($this->qb->expr()->between("$col", ":date_from", ":date_to"));
			$this->qb->setParameter('date_from', $date_from);
			$this->qb->setParameter('date_to', $date_to);
	
	}

	public function addEq($col, $index, $value) {
		$this->qb->andWhere($this->qb->expr()->eq("$col", ":$index"));
		$this->qb->setParameter("$index", "$value");
	}

	public function addLike($col, $index, $value) {
	
		$this->qb->andWhere($this->qb->expr()->like($this->qb->expr()->lower("$col"), ":$index"));
		$this->qb->setParameter("$index", "%". strtolower(trim($value)) ."%");
	}

	public function getInputsActive($inputs, $segmento) {
		foreach ($this->inputsActive as $key => $elem) {
			foreach ($elem as $index => $value) {
				if (!empty($value)) {
					if ($index == 'fechaEvento') {
						if (gettype($value) == 'string') {
							$input = $value;
						} else {
							if (\is_numeric($value)) {
								if ($key == "segmento") {
									$getChildren = "get" . \ucfirst($index);
									$input = $segmento->$getChildren();								
								} else {
									$getChildren = "get" . \ucfirst($index);
									$input = $segmento->getCinta()->$getChildren();
								}
							} else {						
								$input = $value;
							}
						}
					} else {
						if (\is_numeric($value)) {
							if ($key == "segmento") {
								$getChildren = "get" . \ucfirst($index);
								$input = $segmento->$getChildren();								
							} else {
								$getChildren = "get" . \ucfirst($index);
								$input = $segmento->getCinta()->$getChildren();
							}
						} else {						
							$input = $value;
						}
					}

					$this->inputsActive["$key"]["$index"] = $input;
				}
			}
		}

		return $this->inputsActive;
	}

}
