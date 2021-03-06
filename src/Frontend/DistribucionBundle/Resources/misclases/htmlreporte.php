<?php

namespace Frontend\DistribucionBundle\Resources\misclases;
use Frontend\DistribucionBundle\Entity\Operador;

class htmlreporte
{
    public function informativo($em,$datos)
    {
        
        
      	    if($datos=='todo'){
	  		$dql = "
			SELECT r FROM DistribucionBundle:Representante r JOIN r.operador o JOIN o.tipooperador t JOIN o.pais p JOIN o.comodato c
	        where o.estatus=true order by o.pais ASC";
	        $consulta = $em->createQuery($dql);
	        $operador = $consulta->getResult();
                
                $fecha="General";
                
	    }

	    else if($datos['operador']=='t'){
	        $dql = "
			SELECT r FROM DistribucionBundle:Representante r JOIN r.operador o JOIN o.tipooperador t JOIN o.pais p JOIN o.comodato c
	        where o.pais in (:idpais) and o.tipooperador in (:idto) and o.fecharegistro>= :fechadesde and o.fecharegistro<= :fechahasta and o.estatus=true order by o.nombre ASC";
	        $consulta = $em->createQuery($dql);
	        $consulta->setParameter('idpais', $datos['pais']);
	        $consulta->setParameter('idto', $datos['tipooperador']);
	        $consulta->setParameter('fechadesde', $datos['fechadesde']);
	        $consulta->setParameter('fechahasta', $datos['fechahasta']);
	        $operador = $consulta->getResult();	
                $fecha="Fecha desde: ".date("d-m-Y",strtotime($datos['fechadesde']))." | Fecha hasta: ".date("d-m-Y",strtotime($datos['fechahasta']));
	    }

	    else{
	        $dql = "
			SELECT r FROM DistribucionBundle:Representante r JOIN r.operador o JOIN o.tipooperador t JOIN o.pais p JOIN o.comodato c
	        where o.pais in (:idpais) and o.tipooperador in (:idto) and o.id in (:ido) and o.fecharegistro>= :fechadesde and o.fecharegistro<= :fechahasta and o.estatus=true order by o.nombre ASC";
	        $consulta = $em->createQuery($dql);
	        $consulta->setParameter('idpais', $datos['pais']);
	        $consulta->setParameter('idto', $datos['tipooperador']);
	        $consulta->setParameter('ido', $datos['operador']);
	        $consulta->setParameter('fechadesde', $datos['fechadesde']);
	        $consulta->setParameter('fechahasta', $datos['fechahasta']);
	        $operador = $consulta->getResult();
                $fecha="Fecha desde: ".date("d-m-Y",strtotime($datos['fechadesde']))." | Fecha hasta: ".date("d-m-Y",strtotime($datos['fechahasta']));
    	}
        $titulo="";

		if(!empty($operador)){

			$html ="<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
			$html .="
					<table>
						<tr>
							<td class='imagen'><img src='/sait/web/images/logo.jpg' height='150px'></td>
							<td class='titulo' align='center' valing='middle' colspan='8'>REPORTE INFORMATIVO<br>".$titulo."<br> ".$fecha."</td>
						</tr>
					</table>

					<table cellspacing=1 cellpadding='5px' class='table'>

						<tr>
						<td colspan='6' align='center'>DATOS DE OPERADOR</td>
						<td colspan='6' align='center'>DATOS DE REPRESENTANTE</td>
						</tr>
						<tr>
							<th>TIPO</th>
							<th>OPERADOR</th>
							<th>DIAL</th>
							<th>PAIS</th>
							<th>ZONA</th>
							<th>RECEPTOR</th>
							<th>ABONADOS</th>

							<th>NOMBRES</th>
							<th>APELLIDOS</th>
							<th>CARGO</th>
							<th>TELEFONO1</th>
							<th>CORREO</th>
						</tr>
			";

                        $ultimooperador=0; $registros=0; $contador=0; $sumaabonados=0;


			foreach ($operador as $o) {
				$registros=0;

				$operadoractual=$o->getOperador()->getId();

				if($operadoractual!=$ultimooperador){
					$registros=1; $contador++;

					$sumaabonados=$sumaabonados+$o->getOperador()->getNumeroabonados();

					//PARA CONTAR CUANTOS REGISTROS HAY
					$uo=0;$cont=0;
					foreach ($operador as $op) {
						if($operadoractual==$op->getOperador()->getId())
							$cont++;
					}

					if($o->getOperador()->getComodato()->getRecibereceptor()===true) $recibereceptor='Si'; else $recibereceptor='No';
					$html .="<tr>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getTipooperador()."</td>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getNombre()."</td>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getDialUrl()."</td>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getPais()."</td>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getZona()."</td>";
					$html .="<td rowspan=".$cont.">".$recibereceptor."</td>";
					$html .="<td rowspan=".$cont.">".$o->getOperador()->getNumeroabonados()."</td>";
				}

				if($registros!=1)$html .="<tr>";

				$html .="<td>".$o->getNombres()."</td>";
				$html .="<td>".$o->getApellidos()."</td>";
				$html .="<td>".$o->getCargo()."</td>";
				$html .="<td>".$o->getTelefono1()."</td>";
				$html .="<td>".$o->getCorreo()."</td>";
				$html .="</tr>";
				
				$ultimooperador=$o->getOperador()->getId();
			}
			$html .="<tr><td></td><td></td><td></td><td colspan=3 align=right><b>Total Abonados:</b></td><td><b>".$sumaabonados."</b></td><td colspan='5'><b>Total Operadores: ".$contador."</b></td></tr></table>";
			$html .="</table>";
		}else{
			return false;
		}
		
		return $html;
    }
}
