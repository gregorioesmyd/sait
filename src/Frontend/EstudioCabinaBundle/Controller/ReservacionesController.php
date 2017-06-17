<?php

namespace Frontend\EstudioCabinaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Frontend\EstudioCabinaBundle\Entity\Reservaciones;
use Frontend\EstudioCabinaBundle\Entity\HistorialReservaciones;
use Frontend\EstudioCabinaBundle\Entity\Protocolo;
use Frontend\EstudioCabinaBundle\Form\ReservacionesType;
use Frontend\EstudioCabinaBundle\Form\ProtocoloType;

/**
 * Reservaciones controller.
 *
 */
class ReservacionesController extends Controller
{

    /**
     * Lists all Reservaciones entities.
     *
     */
    public function indexAction($tipo,$ubicacion)
    {

    	$em = $this->getDoctrine()->getManager();

    	$query = $em
    	->createQuery(
    		'select x from EstudioCabinaBundle:Reservaciones x join x.estudioCabina c
    		WHERE c.tipo = :tipo'
    		)->setParameter('tipo', $tipo);
    	$entities = $query->getResult();
    	if($entities){
    		foreach ($entities as $datos){

    			$idusuario = $datos->getPerfilId();

                          //consulto los datos del usuario que creo la pauta 
    			$dql    = "select p from UsuarioBundle:Perfil p where p.id=$idusuario";
    			$query  = $em->createQuery($dql);
    			$perfil = $query->getResult();
    			$usuario[$datos->getId()]['nombres_apellidos'] = $perfil[0]->getPrimerNombre() .' '.$perfil[0]->getPrimerApellido();
    			$usuario[$datos->getId()]['unidad'] = "";
    			if($perfil[0]->getNivelorganizacional())
    			{
    				$usuario[$datos->getId()]['unidad'] = $perfil[0]->getNivelorganizacional()->getDescripcion();
    			}
    			$usuario[$datos->getId()]['extension'] = $perfil[0]->getExtension();
                    //fin 

    			$actual         = strtotime(date_format(new \DateTime("now"), 'd-m-Y')) ;
    			$fechabd        = strtotime(date_format($datos->getFecha(), 'd-m-Y'));

    			if ($actual > $fechabd and $datos->getEstatus()!= 1 and $datos->getEstatus()!= 3){
    				$datos->setEstatus(4);
    			}
    			if ($actual > $fechabd and $datos->getEstatus()== 1 ){
    				$datos->setEstatus(5);
    			}
    		}
    	} else {
    		$usuario = array();
    	}

    	return $this->render('EstudioCabinaBundle:Reservaciones:index.html.twig', array(
    		'entities'          => $entities,
    		'tipo'              => $tipo,
            'ubicacion'              => $ubicacion,
    		'perfil'            => $usuario
    		));
    }
    
    public function listaAction($tipo,$ubicacion)
    {
    	$em = $this->getDoctrine()->getManager();
    	$idusuario = $this->get('security.context')->getToken()->getUser()->getId();

    	$query = $em
    	->createQuery(
    		'select x from EstudioCabinaBundle:Reservaciones x join x.estudioCabina c
    		WHERE c.tipo = :tipo and x.perfilId = :idusuario'
    		)->setParameter('tipo', $tipo)
    	->setParameter('idusuario', $idusuario);
    	$entities = $query->getResult();
    	if($entities){
    		foreach ($entities as $datos){

    			$idusuario = $datos->getPerfilId();

                  //consulto los datos del usuario que creo la pauta 
    			$dql    = "select p from UsuarioBundle:Perfil p where p.id=$idusuario";
    			$query  = $em->createQuery($dql);
    			$perfil = $query->getResult();
    			$usuario[$datos->getId()]['nombres_apellidos'] = $perfil[0]->getPrimerNombre() .' '.$perfil[0]->getPrimerApellido();
    			$usuario[$datos->getId()]['unidad'] = $perfil[0]->getNivelorganizacional()->getDescripcion();
    			$usuario[$datos->getId()]['extension'] = $perfil[0]->getExtension();
            //fin 

    			$actual         = strtotime(date_format(new \DateTime("now"), 'd-m-Y')) ;
    			$fechabd        = strtotime(date_format($datos->getFecha(), 'd-m-Y'));

    			if ($actual > $fechabd and $datos->getEstatus()!= 1 and $datos->getEstatus()!= 3){
    				$datos->setEstatus(4);
    			}
    			if ($actual > $fechabd and $datos->getEstatus()== 1 ){
    				$datos->setEstatus(5);
    			}
    		}
    	} else {
    		$usuario = array();
    	}
    	return $this->render('EstudioCabinaBundle:Reservaciones:index.html.twig', array(
    		'entities'          => $entities,
    		'tipo'              => $tipo,
            'ubicacion'         => $ubicacion,
    		'perfil'            => $usuario
    		));
    }
    

    /**
     * Creates a new Reservaciones entity.
     *
     */
    public function createAction(Request $request,$tipo,$ubicacion)
    {   

    	$em = $this->getDoctrine()->getManager();

    	$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
    	$perfil = $em->getRepository('UsuarioBundle:Perfil')
       				  ->findOneByUser($idusuario);
                      
    	$entity = new Reservaciones();
    	$form = $this->createCreateForm($ubicacion,$tipo,$entity);
    	$form->handleRequest($request);

        $entity2 = new Protocolo();
        $form2 = $this->createForm(new ProtocoloType(), $entity2);
        $form2->handleRequest($request);

    if($tipo == 3){

    		if ($form->isValid() && $form2->isValid()) {
            //CONSULTO LOS DATOS DEL USUARIO
    			$entity->setPerfilId($idusuario);
            //FIN
            //Capturo los datos para validar que la fecha elegida sea mayor que la actual 
    			$fechahoy = new \DateTime();
    			$fechaactual=  date_format($fechahoy, 'd-m-Y');
    			$actual = strtotime($fechaactual);  
    			$fecha_entrada = date_format($entity->getFecha(),'d-m-Y'); 
    			$entrada = strtotime($fecha_entrada);
            //Fin  
    			if($entrada < $actual){  
    				$this->get('session')->getFlashBag()->add('alert', 'La fecha debe ser mayor o igual que la fecha actual..');
    				return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    					'entity' => $entity,
    					'form'   => $form->createView(),
    					'form2'   => $form2->createView(),
    					'perfil' => $perfil,
    					'tipo'   => $tipo,
                        'ubicacion'   => $ubicacion,
    					));
    			}

                //Datos capturado en el formulario
    			$fecha           = date_format($entity->getFecha(),'Y-m-d');
                     //resto un minuto a la hora de inicio y fin
    			$horai = strtotime ('+1 minute' , strtotime (date_format($entity->getHorainicio(),'G:i:s')));
    			$horaf = strtotime ('-1 minute' , strtotime (date_format($entity->getHorafin(), 'G:i:s')));
    			$horaini         = date('G:i:s',$horai);
    			$horafin         = date('G:i:s',$horaf);
                     //Fin
    			$hora1           = strtotime(date_format($entity->getHorainicio(),'G:i:s'));
    			$hora2           = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
    			$idEC            = $entity->getEstudioCabina()->getId();
    			$fechaelegida    =  strtotime($fecha);
    			list($sdiaRegistro)= explode(':', date('w', $fechaelegida));
                //Fin
               //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
    			if($hora1 > $hora2){
    				$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
    				return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    					'entity' => $entity,
    					'form'   => $form->createView(),
    					'form2'   => $form2->createView(),
    					'perfil' => $perfil,
    					'tipo'   => $tipo,
                        'ubicacion'   => $ubicacion,
    					));
    			}

    			if($hora1 == $hora2){
    				$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
    				return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    					'entity' => $entity,
    					'form'   => $form->createView(),
    					'form2'   => $form2->createView(),
    					'perfil' => $perfil,
    					'tipo'   => $tipo,
                        'ubicacion'   => $ubicacion,
    					));
    			}


                 //Realizo la consulta en base de dato 
                //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE

    			$pauta = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
    			$pauta->setParameter(1, $horaini);
    			$pauta->setParameter(2, $horafin);
    			$pauta->setParameter(3, $idEC);
    			$sqlpf = $pauta->getResult();
                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA   
    			$sqlp = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( x.horainicio BETWEEN ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
    			$sqlp->setParameter(1, $horaini);
    			$sqlp->setParameter(2, $horafin);
    			$sqlp->setParameter(3, $idEC);
    			$spauta = $sqlp->getResult();

                //Fin
                //CONSULTO LAS RESERVACIONES PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
    			$resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
    			$resevaciones->setParameter(1, $horaini);
    			$resevaciones->setParameter(2, $horafin);
    			$resevaciones->setParameter(3, $idEC);
    			$reserva = $resevaciones->getResult();
                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
    			$resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
    			$resevas->setParameter(1, $horaini);
    			$resevas->setParameter(2, $horafin);
    			$resevas->setParameter(3, $idEC);
    			$reser = $resevas->getResult();
                 //FIN
                //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA
    			if($sqlpf){
    				foreach ($sqlpf as $pautas) {

    					$hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
    					$hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
    					$dias = $pautas->getDiasPautas();
    					$arrayDay = explode(",", $dias);

    					if (in_array($sdiaRegistro, $arrayDay)) {
    						$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
    						return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    							'entity' => $entity,
    							'form' => $form->createView(),
    							'form2'   => $form2->createView(),
    							'perfil' => $perfil,
    							'tipo' => $tipo,
                                'ubicacion'   => $ubicacion,
    							));
    					}
    				}
    			}

    			if($spauta){
    				foreach ($spauta as $pauts) {

    					$hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
    					$hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
    					$dias = $pauts->getDiasPautas();
    					$arrayDay = explode(",", $dias);

    					if (in_array($sdiaRegistro, $arrayDay)) {

    						$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
    						return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    							'entity' => $entity,
    							'form'   => $form->createView(),
    							'form2'   => $form2->createView(),
    							'perfil' => $perfil,
    							'tipo'   => $tipo,
                                'ubicacion'   => $ubicacion,
    							));
    					}
    				}
    			}

    			if($reser){
    				foreach ($reser as $reservas){

    					$hora1bdr        =  strtotime(date_format($reservas->getHorainicio(),'G:i:s'));
    					$hora2bdr        =  strtotime(date_format($reservas->getHorafin(),'G:i:s'));
    					$fechabd         =  strtotime(date_format($reservas->getFecha(),'Y-m-d'));
    					$fechaelegida;

    					if($fechaelegida == $fechabd){
    						$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
    						return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    							'entity' => $entity,
    							'form' => $form->createView(),
    							'form2'   => $form2->createView(),
    							'perfil' => $perfil,
    							'tipo' => $tipo,
                                'ubicacion'   => $ubicacion,
    							));
    					}
    				} 
    			}

    			if($reserva){

    				foreach ($reserva as $reservacion){
    					$hora1bdr        =  strtotime(date_format($reservacion->getHorainicio(),'G:i:s'));
    					$hora2bdr        =  strtotime(date_format($reservacion->getHorafin(),'G:i:s'));
    					$fechabd         =  strtotime(date_format($reservacion->getFecha(),'Y-m-d'));
    					$fechaelegida;

    					if($fechaelegida == $fechabd){
    						$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
    						return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
    							'entity' => $entity,
    							'form' => $form->createView(),
    							'form2'   => $form2->createView(),
    							'perfil' => $perfil,
    							'tipo' => $tipo,
                                'ubicacion'   => $ubicacion,
    							));
    					}
    				}
    			}

    			$em->persist($entity);
            //Si es una reservacion de sala, inserto en la tabla protocolo nro perosnas, y refrigerio
    			$refrigerio = '';
    			$refrige=array_filter($entity2->getRefrigerio(),'strlen');
    			$c = count($refrige);
    			foreach ($refrige as $refri){
    				if($c > 1) {
    					$refrigerio .= $refri.',';
    				} else {
    					$refrigerio .= $refri;
    				}
    				--$c;
    			}

    			$entity2->setReservacion($entity);
    			$entity2->setRefrigerio($refrigerio);
    			$em->persist($entity2); 


             //Fin     
            //Creo mi objeto para guardar el historial de cuando se creo la solicitud
    			$historial       = new HistorialReservaciones();
                //$idPerfil        = $entity->setPerfilId($idusuario);
    			$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
    			$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
            //Seteo los datos para guardarlo
    			$historial ->setEstatus($entity->getEstatus());
    			$historial->setPerfil($idusuario);
    			$historial->setReservaciones($idreservacion);
    			$historial->setFechaHora($fecha_operacion);

    			$em->persist($historial);
    			$em->flush();     
            //envio correo
                      $responsable=$entity->getPerfilId();
                        $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                        $correouser=$datouser->getUsername()."@telesurtv.net";
                        $subject= $entity->getEstudioCabina()->getNombre();
                                   
                       if($tipo ==3){
                        //Verifico que sala fue seleccionada para notificarle al coordinador 
                            if ($entity->getEstudioCabina()->getNombre()=='SALA DE PROYECCIONES'){
                                $parametros['sproyecciones']=$this->container->getParameter('sproyecciones');
                                $sala=explode(',',$parametros['sproyecciones']);
                            }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #1'){
                                $parametros['sala1']=$this->container->getParameter('sala1');
                                $sala=explode(',',$parametros['sala1']);
                            }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #4'){
                                $parametros['sala4']=$this->container->getParameter('sala4');
                                $sala=explode(',',$parametros['sala4']);
                            }

                            $correoto=array_push($sala,$correouser);
                        //Fin
                            $correoto = $sala;
                       }
                         
                             $correoaplica="aplicaciones@telesurtv.net";
                             $message = \Swift_Message::newInstance()   
                            ->setSubject('RESERVACIÓN DE'.' '.$subject)  
                            ->setFrom($correoaplica)     // we configure the sender
                            ->setTo($correoto)   
                            ->setBody( $this->renderView(
                                'EstudioCabinaBundle:Correo:solicitud.html.twig',
                                array('reservaciones' => $entity,
                                      'perfil'        => $perfil)
                            ), 'text/html');

                        $this->get('mailer')->send($message);
            //fin enviar correo
               

                        $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA REALIZADO EXITOSAMENTE');

                        return $this->redirect($this->generateUrl('reservaciones_show',array(
                            'id' => $entity->getId(),
                            'ubicacion' => $ubicacion)));
                    }
                    return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                                'entity' => $entity,
                                'form' => $form->createView(),
                                'form2'   => $form2->createView(),
                                'perfil' => $perfil,
                                'tipo' => $tipo,
                                'ubicacion'   => $ubicacion,
                                ));
                }else{
                	if ($form->isValid()) {
            //CONSULTO LOS DATOS DEL USUARIO
                		$entity->setPerfilId($idusuario);
            //FIN
            //Capturo los datos para validar que la fecha elegida sea mayor que la actual 
                		$fechahoy = new \DateTime();
                		$fechaactual=  date_format($fechahoy, 'd-m-Y');
                		$actual = strtotime($fechaactual);  
                		$fecha_entrada = date_format($entity->getFecha(),'d-m-Y'); 
                		$entrada = strtotime($fecha_entrada);
            //Fin  
                		if($entrada < $actual){  
                			$this->get('session')->getFlashBag()->add('alert', 'La fecha debe ser mayor o igual que la fecha actual..');
                			return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                				'entity' => $entity,
                				'form'   => $form->createView(),
                				'perfil' => $perfil,
                				'tipo'   => $tipo,
                                'ubicacion'   => $ubicacion,
                				));
                		}

                //Datos capturado en el formulario
                		$fecha           = date_format($entity->getFecha(),'Y-m-d');
                     //resto un minuto a la hora de inicio y fin
                		$horai = strtotime ('+1 minute' , strtotime (date_format($entity->getHorainicio(),'G:i:s')));
                		$horaf = strtotime ('-1 minute' , strtotime (date_format($entity->getHorafin(), 'G:i:s')));
                		$horaini         = date('G:i:s',$horai);
                		$horafin         = date('G:i:s',$horaf);
                     //Fin
                		$hora1           = strtotime(date_format($entity->getHorainicio(),'G:i:s'));
                		$hora2           = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
                		$idEC            = $entity->getEstudioCabina()->getId();
                		$fechaelegida    =  strtotime($fecha);
                		list($sdiaRegistro)= explode(':', date('w', $fechaelegida));
                //Fin
               //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
                		if($hora1 > $hora2){
                			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                			return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                				'entity' => $entity,
                				'form'   => $form->createView(),
                				'perfil' => $perfil,
                				'tipo'   => $tipo,
                                'ubicacion'   => $ubicacion,
                				));
                		}

                		if($hora1 == $hora2){
                			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                			return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                				'entity' => $entity,
                				'form'   => $form->createView(),
                				'perfil' => $perfil,
                				'tipo'   => $tipo,
                                'ubicacion'   => $ubicacion,
                				));
                		}


                 //Realizo la consulta en base de dato 
                //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE

                		$pauta = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
                		$pauta->setParameter(1, $horaini);
                		$pauta->setParameter(2, $horafin);
                		$pauta->setParameter(3, $idEC);
                		$sqlpf = $pauta->getResult();
                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA   
                		$sqlp = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( x.horainicio BETWEEN ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
                		$sqlp->setParameter(1, $horaini);
                		$sqlp->setParameter(2, $horafin);
                		$sqlp->setParameter(3, $idEC);
                		$spauta = $sqlp->getResult();

                //Fin
                //CONSULTO LAS RESERVACIONES PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
                		$resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
                		$resevaciones->setParameter(1, $horaini);
                		$resevaciones->setParameter(2, $horafin);
                		$resevaciones->setParameter(3, $idEC);
                		$reserva = $resevaciones->getResult();
                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
                		$resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
                		$resevas->setParameter(1, $horaini);
                		$resevas->setParameter(2, $horafin);
                		$resevas->setParameter(3, $idEC);
                		$reser = $resevas->getResult();
                 //FIN
                //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA
                		if($sqlpf){
                			foreach ($sqlpf as $pautas) {

                				$hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
                				$hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
                				$dias = $pautas->getDiasPautas();
                				$arrayDay = explode(",", $dias);

                				if (in_array($sdiaRegistro, $arrayDay)) {
                					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                					return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                						'entity' => $entity,
                						'form'   => $form->createView(),
                						'perfil' => $perfil,
                						'tipo'   => $tipo,
                                        'ubicacion'   => $ubicacion,
                						));
                				}
                			}
                		}

                		if($spauta){
                			foreach ($spauta as $pauts) {

                				$hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
                				$hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
                				$dias = $pauts->getDiasPautas();
                				$arrayDay = explode(",", $dias);

                				if (in_array($sdiaRegistro, $arrayDay)) {

                					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                					return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                						'entity' => $entity,
                						'form'   => $form->createView(),
                						'perfil' => $perfil,
                						'tipo'   => $tipo,
                                        'ubicacion'   => $ubicacion,
                						));
                				}
                			}
                		}

                                if($reser){
                			foreach ($reser as $reservas){

                				$hora1bdr        =  strtotime(date_format($reservas->getHorainicio(),'G:i:s'));
                				$hora2bdr        =  strtotime(date_format($reservas->getHorafin(),'G:i:s'));
                				$fechabd         =  strtotime(date_format($reservas->getFecha(),'Y-m-d'));
                				$fechaelegida;

                				if($fechaelegida == $fechabd){
                					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                					return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                						'entity' => $entity,
                						'form'   => $form->createView(),
                						'perfil' => $perfil,
                						'tipo'   => $tipo,
                                        'ubicacion'   => $ubicacion,
                						));
                				}
                			} 
                		}

                		if($reserva){

                			foreach ($reserva as $reservacion){
                				$hora1bdr        =  strtotime(date_format($reservacion->getHorainicio(),'G:i:s'));
                				$hora2bdr        =  strtotime(date_format($reservacion->getHorafin(),'G:i:s'));
                				$fechabd         =  strtotime(date_format($reservacion->getFecha(),'Y-m-d'));
                				$fechaelegida;

                				if($fechaelegida == $fechabd){
                					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                					return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                						'entity' => $entity,
                						'form'   => $form->createView(),
                						'perfil' => $perfil,
                						'tipo'   => $tipo,
                                        'ubicacion'   => $ubicacion,
                						));
                				}
                			}
                		}

                		$em->persist($entity);   
            //Creo mi objeto para guardar el historial de cuando se creo la solicitud
                		$historial       = new HistorialReservaciones();
                //$idPerfil        = $entity->setPerfilId($idusuario);
                		$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
                		$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
            //Seteo los datos para guardarlo
                		$historial ->setEstatus($entity->getEstatus());
                		$historial->setPerfil($idusuario);
                		$historial->setReservaciones($idreservacion);
                		$historial->setFechaHora($fecha_operacion);

                		$em->persist($historial);
                		$em->flush();     
            //envio correo
                        $responsable=$entity->getPerfilId();
                        $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                        $correouser=$datouser->getUsername()."@telesurtv.net";
                        $subject= $entity->getEstudioCabina()->getNombre();
                                   
                       if($tipo==1){
                            $parametros['estudio']=$this->container->getParameter('estudio');
                            $estudio=explode(',',$parametros['estudio']);
                            $correoto = array_push($estudio,$correouser);
                            $correoto = $estudio; 
                       }elseif($tipo == 2){
                            $parametros['cabina']=$this->container->getParameter('cabina');
                            $cabina=explode(',',$parametros['cabina']);
                            $correoto = array_push($cabina,$correouser);
                            $correoto = $cabina;
                       }
                         
                             $correoaplica="aplicaciones@telesurtv.net";
                             $message = \Swift_Message::newInstance()   
                            ->setSubject('RESERVACIÓN DE'.' '.$subject)  
                            ->setFrom($correoaplica)     // we configure the sender
                            ->setTo($correoto)   
                            ->setBody( $this->renderView(
                                'EstudioCabinaBundle:Correo:solicitud.html.twig',
                                array('reservaciones' => $entity,
                                      'perfil'        => $perfil)
                            ), 'text/html');

                        $this->get('mailer')->send($message);   
            
            //fin enviar correo
                

                        $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA REALIZADO EXITOSAMENTE');

                        return $this->redirect($this->generateUrl('reservaciones_show',array('id' => $entity->getId(),'ubicacion'=>$ubicacion)));
                    }
                }


                return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
                	'entity' => $entity,
                	'form'   => $form->createView(),
                	'perfil' => $perfil,
                	'tipo'   => $tipo,
                    'ubicacion'   => $ubicacion,
                	));

            }

    /**
     * Creates a form to create a Reservaciones entity.
     *
     * @param Reservaciones $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($ubicacion,$tipo,Reservaciones $entity)
    {
    	$form = $this->createForm(new ReservacionesType($tipo), $entity, array(
    		'action' => $this->generateUrl('reservaciones_create', array(
            'tipo' => $tipo,
            'ubicacion' => $ubicacion)),
    		'method' => 'POST',
    		));

    	$form->add('submit', 'submit', array('label' => 'Create'));

    	return $form;
    }

    /**
     * Displays a form to create a new Reservaciones entity.
     *
     */
    public function newAction($tipo,$ubicacion = 0)
    {
        $em = $this->getDoctrine()->getManager();

    	$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')
        ->findOneByUser($idusuario);
        $entity = new Reservaciones();
        $form   = $this->createForm(new ReservacionesType($tipo), $entity);

        $entity2 = new Protocolo();
        $form2   = $this->createForm(new ProtocoloType(), $entity2);
        
        return $this->render('EstudioCabinaBundle:Reservaciones:new.html.twig', array(
        	'entity'       => $entity,
        	'form'         => $form->createView(),
        	'form2'        => $form2->createView(),
        	'perfil'       => $perfil,
        	'tipo'         => $tipo,
            'ubicacion'    => $ubicacion
        	));
    }
    

    /**
     * Finds and displays a Reservaciones entity.
     *
     */
    public function showAction($id,$ubicacion)
    {
        
    	$em = $this->getDoctrine()->getManager();

    	$entity         = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
    	$tipo           = $entity->getEstudioCabina()->getTipo();
    	$idusuario      = $entity->getPerfilId();
    	$actual         = strtotime(date_format(new \DateTime("now"), 'd-m-Y')) ;
    	$fechabd        = strtotime(date_format($entity->getFecha(), 'd-m-Y'));
        //consulto los datos del usuario que creo la pauta 
    	$dql = "select p from UsuarioBundle:Perfil p where p.id=$idusuario";
    	$query = $em->createQuery($dql);
    	$entities = $query->getResult();

    	$user = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true";
    	$sql = $em->createQuery($user);
    	$postproductor = $sql->getResult();
        //fin 
    	if ($actual > $fechabd and $entity->getEstatus()!= 1 and $entity->getEstatus()!= 3){
    		$entity->setEstatus(4);
    	}
    	if ($actual > $fechabd and $entity->getEstatus()== 1 ){
    		$entity->setEstatus(5);
    	}

    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Reservaciones entity.');
    	}

        return $this->render('EstudioCabinaBundle:Reservaciones:show.html.twig', array(
    		'entity'        => $entity,
    		'perfil'        => $entities,
    		'tipo'          => $tipo,
            'ubicacion'     => $ubicacion,
    		'postproductor' => $postproductor,   
    		));
    }

    
    /**
     * Displays a form to edit an existing Reservaciones entity.
     *
     */
    public function editAction($id,$ubicacion = 0)
    {
       
    	$em = $this->getDoctrine()->getManager();

    	$query = $em->createQuery(
			    		'select x from EstudioCabinaBundle:Protocolo x join x.reservacion c
			    		WHERE c.id = :id'
			    		)->setParameter('id', $id);
    			$entity2 = $query->getResult();
			if($entity2){
				foreach ($entity2 as $entities) {

	    		$tipo   = $entities->getReservacion()->getEstudioCabina()->getTipo(); 
	    		$refrigerio = $entities->getRefrigerio();
	    		$refri = explode(",", $refrigerio);
				$entities->setRefrigerio($refri);
	    		$entity = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);

			        $editForm = $this->createEditForm($ubicacion,$tipo,$entity);
			        $form2 = $this->createForm(new ProtocoloType(), $entities, array(
				    		'action' => $this->generateUrl('reservaciones_update', array(
                            'id' => $entities->getId(),
                            'ubicacion' => $ubicacion)),
				    		'method' => 'PUT',
				    		));
			        return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
			        	'entity'      => $entity,
			        	'entity2'     => $entity2,
			        	'edit_form'   => $editForm->createView(),
			        	'edit_form2'  => $form2->createView(),
			        	'tipo'        => $tipo,
                        'ubicacion'   => 0,
			        	));  
	    		}
	    	}else{
			 $entity = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
			 $tipo   = $entity->getEstudioCabina()->getTipo(); 
		        if (!$entity) {
		        	throw $this->createNotFoundException('Unable to find Reservaciones entity.');
		        }

		        $editForm = $this->createEditForm($ubicacion,$tipo,$entity);
		        return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		        	'entity'      => $entity,
		        	'edit_form'   => $editForm->createView(),
		        	'tipo'        => $tipo,
                    'ubicacion'   => 0,
		        	));
			}
    }

    /**
    * Creates a form to edit a Reservaciones entity.
    *
    * @param Reservaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm($ubicacion,$tipo,Reservaciones $entity)
    { 
        $form = $this->createForm(new ReservacionesType($tipo), $entity, array(
    		'action' => $this->generateUrl('reservaciones_update', array(   
            'id' => $entity->getId(),
            'ubicacion' => $ubicacion)),
    		'method' => 'PUT',
    		));

    	$form->add('submit', 'submit', array('label' => 'Actualizar'));

    	return $form;
    }
    /**
     * Edits an existing Reservaciones entity.
     *
     */
    public function updateAction(Request $request, $id,$ubicacion)
    {
        
        $em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
		$tipo   = $entity->getEstudioCabina()->getTipo(); 
    	$query = $em->createQuery(
			    		'select x from EstudioCabinaBundle:Protocolo x join x.reservacion c
			    		WHERE c.id = :id'
			    		)->setParameter('id', $id);
    			$entity2 = $query->getResult();
			if($entity2){
				foreach ($entity2 as $entities) {

				 $refrigerio = $entities->getRefrigerio();
	    		 $refri = explode(",", $refrigerio);
				 $entities->setRefrigerio($refri);

				 $editForm = $this->createEditForm($ubicacion,$tipo,$entity);
    			 $editForm->handleRequest($request);

    			 $editForm2 = $this->createForm(new ProtocoloType(), $entities, array(
			    		'action' => $this->generateUrl('reservaciones_update', array(
                        'id' => $entities->getId(),
                        'ubicacion' => $ubicacion)),
			    		'method' => 'PUT',
			    		));
    			 $editForm2->handleRequest($request);

    			 if ($entities->getRefrigerio()) {
					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un tipo de refrigerio..');
					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
			    						 'entity' 		=> $entity,
			    						 'edit_form'    => $editForm->createView(),
			    						 'edit_form2'   => $editForm2->createView(),
			    						 'tipo' 		=> $tipo,
                                         'ubicacion'    => $ubicacion
			    						));
	    		}

	    		if ($entities->getNroPersonas()) {
					$this->get('session')->getFlashBag()->add('alert', 'Debe indicar el numero de personas a asistir..');
					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
			    						 'entity' 		=> $entity,
			    						 'edit_form'    => $editForm->createView(),
			    						 'edit_form2'   => $editForm2->createView(),
			    						 'tipo' 		=> $tipo,
                                         'ubicacion'    => $ubicacion
			    						));
	    		}

					if ($editForm->isValid()) {

			            //Capturo los datos para validar que la fecha elegida sea mayor que la actual 
			    		$fechahoy = new \DateTime();
			    		$fechaactual=  date_format($fechahoy, 'd-m-Y');
			    		$actual = strtotime($fechaactual);  
			    		$fecha_entrada = date_format($entity->getFecha(),'d-m-Y'); 
			    		$entrada = strtotime($fecha_entrada);
			            //Fin  
			    		if($entrada < $actual){  
			    			$this->get('session')->getFlashBag()->add('alert', 'La fecha debe ser mayor o igual que la fecha actual..');
			    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    				 'entity' 		=> $entity,
							    				 'edit_form'  	=> $editForm->createView(),
							    				 'edit_form2'   => $editForm2->createView(),
							    				 'tipo' 		=> $tipo,
                                                 'ubicacion'    => $ubicacion
							    				));
			    		}
					//Datos capturado en el formulario
			    		$fecha           = date_format($entity->getFecha(),'Y-m-d');
			            //resto un minuto a la hora de inicio y fin
			    		$horai = strtotime ('+1 minute' , strtotime (date_format($entity->getHorainicio(),'G:i:s')));
			    		$horaf = strtotime ('-1 minute' , strtotime (date_format($entity->getHorafin(), 'G:i:s')));
			    		$horaini         = date('G:i:s',$horai);
			    		$horafin         = date('G:i:s',$horaf);
			           //Fin
			    		$hora1           = strtotime(date_format($entity->getHorainicio(),'G:i:s'));
			    		$hora2           = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
			    		$idEC            = $entity->getEstudioCabina()->getId();
			    		$fechaelegida    =  strtotime($fecha);
			    		list($sdiaRegistro)= explode(':', date('w', $fechaelegida));
			            //Fin

			               //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
			    		if($hora1 > $hora2){
			    			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
			    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    				 'entity'         => $entity,
							    				 'edit_form'      => $editForm->createView(),
							    				 'edit_form2'     => $editForm2->createView(),
							    				 'tipo'           => $tipo,
                                                 'ubicacion'      => $ubicacion
			    								));
			    		}

			    		if($hora1 == $hora2){
			    			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
			    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    				 'entity' => $entity,
							    				 'edit_form'   => $editForm->createView(),
							    				 'edit_form2'   => $editForm2->createView(),
							    				 'tipo' => $tipo,
                                                 'ubicacion'=> $ubicacion
							    				));
			    		}
			                 //Realizo la consulta en base de dato 
			                //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE

			    		$pauta = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
			    		$pauta->setParameter(1, $horaini);
			    		$pauta->setParameter(2, $horafin);
			    		$pauta->setParameter(3, $idEC);
			    		$sqlpf = $pauta->getResult();
			                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA   
			    		$sqlp = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( x.horainicio BETWEEN ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
			    		$sqlp->setParameter(1, $horaini);
			    		$sqlp->setParameter(2, $horafin);
			    		$sqlp->setParameter(3, $idEC);
			    		$spauta = $sqlp->getResult();

			                //Fin
			                //CONSULTO LAS RESERVACIONES PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
			    		$resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.id !='.$id.' and x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
			    		$resevaciones->setParameter(1, $horaini);
			    		$resevaciones->setParameter(2, $horafin);
			    		$resevaciones->setParameter(3, $idEC);
			    		$reserva = $resevaciones->getResult();

			                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
			    		$resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.id !='.$id.' and x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
			    		$resevas->setParameter(1, $horaini);
			    		$resevas->setParameter(2, $horafin);
			    		$resevas->setParameter(3, $idEC);
			    		$reser = $resevas->getResult();
			                 //FIN
			                //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA
			    		if($sqlpf){
			    			foreach ($sqlpf as $pautas) {

			    				$hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
			    				$hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
			    				$dias = $pautas->getDiasPautas();
			    				$arrayDay = explode(",", $dias);

			    				if (in_array($sdiaRegistro, $arrayDay)) {
			    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
			    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    						 'entity' 		=> $entity,
							    						 'edit_form'    => $editForm->createView(),
							    						 'edit_form2'   => $editForm2->createView(),
							    						 'tipo' 		=> $tipo,
                                                         'ubicacion'=> $ubicacion
							    						));
			    				}
			    			}
			    		}

			    		if($spauta){
			    			foreach ($spauta as $pauts) {

			    				$hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
			    				$hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
			    				$dias = $pauts->getDiasPautas();
			    				$arrayDay = explode(",", $dias);

			    				if (in_array($sdiaRegistro, $arrayDay)) {

			    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
			    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    						 'entity' 		=> $entity,
							    						 'edit_form'    => $editForm->createView(),
							    						 'edit_form2'   => $editForm2->createView(),
							    						 'tipo' 		=> $tipo,
                                                         'ubicacion'=> $ubicacion
							    						));
			    				}
			    			}
			    		}

			    		if($reser){
			    			foreach ($reser as $reservas){

			    				$hora1bdr        =  strtotime(date_format($reservas->getHorainicio(),'G:i:s'));
			    				$hora2bdr        =  strtotime(date_format($reservas->getHorafin(),'G:i:s'));
			    				$fechabd         =  strtotime(date_format($reservas->getFecha(),'Y-m-d'));
			    				$fechaelegida;

			    				if($fechaelegida == $fechabd){
			    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
			    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    						 'entity' 		=> $entity,
							    						 'edit_form'    => $editForm->createView(),
							    						 'edit_form2'   => $editForm2->createView(),
							    						 'tipo' 		=> $tipo,
                                                         'ubicacion'=> $ubicacion
							    						));
			    				}
			    			} 
			    		}

			    		if($reserva){

			    			foreach ($reserva as $reservacion){
			    				$hora1bdr        =  strtotime(date_format($reservacion->getHorainicio(),'G:i:s'));
			    				$hora2bdr        =  strtotime(date_format($reservacion->getHorafin(),'G:i:s'));
			    				$fechabd         =  strtotime(date_format($reservacion->getFecha(),'Y-m-d'));
			    				$fechaelegida;

			    				if($fechaelegida == $fechabd){
			    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
			    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
							    						 'entity' 		=> $entity,
							    						 'edit_form'   	=> $editForm->createView(),
							    						 'edit_form2'   => $editForm2->createView(),
							    						 'tipo' 		=> $tipo,
                                                         'ubicacion'=> $ubicacion
							    						));
			    				}
			    			}
			    		}
					//Si es una reservacion de sala, inserto en la tabla protocolo nro perosnas, y refrigerio
		    			$refrigerio = '';
		    			$refrige=array_filter($entities->getRefrigerio(),'strlen');
		    			$c = count($refrige);
		    			foreach ($refrige as $refri){
		    				if($c > 1) {
		    					$refrigerio .= $refri.',';
		    				} else {
		    					$refrigerio .= $refri;
		    				}
		    				--$c;
		    			}

		    			$entities->setRefrigerio($refrigerio);
			    		$em->persist($entity);
			    		$em->persist($entities);
				        $em->flush();

			    		$this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA EDITADO EXITOSAMENTE..');

			    		return $this->redirect($this->generateUrl('reservaciones_show',array(
                            'id' => $entity->getId(),
                            'ubicacion' => $ubicacion)));
			    	}

				}
				return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		    						 'entity' 		=> $entity,
		    						 'edit_form'   	=> $editForm->createView(),
		    						 'edit_form2'   => $editForm2->createView(),
		    						 'tipo' 		=> $tipo,
                                     'ubicacion'    => $ubicacion
		    						));

			}else{
				
				$editForm = $this->createEditForm($ubicacion,$tipo,$entity);
		    	$editForm->handleRequest($request);

    			if ($editForm->isValid()) {
    				 //Capturo los datos para validar que la fecha elegida sea mayor que la actual 
		    		   	$fechahoy = new \DateTime();
		    		   	$fechaactual=  date_format($fechahoy, 'd-m-Y');
		    		   	$actual = strtotime($fechaactual);  
		    		   	$fecha_entrada = date_format($entity->getFecha(),'d-m-Y'); 
		    		   	$entrada = strtotime($fecha_entrada);
		            //Fin  
	    		if($entrada < $actual){  
	    			$this->get('session')->getFlashBag()->add('alert', 'La fecha debe ser mayor o igual que la fecha actual..');
	    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
	    				'entity' => $entity,
	    				'edit_form'   => $editForm->createView(),
	    				'tipo' => $tipo,
                        'ubicacion'=> $ubicacion
	    				));
	    		}

	            //Datos capturado en el formulario
	    		$fecha           = date_format($entity->getFecha(),'Y-m-d');
	            //resto un minuto a la hora de inicio y fin
	    		$horai = strtotime ('+1 minute' , strtotime (date_format($entity->getHorainicio(),'G:i:s')));
	    		$horaf = strtotime ('-1 minute' , strtotime (date_format($entity->getHorafin(), 'G:i:s')));
	    		$horaini         = date('G:i:s',$horai);
	    		$horafin         = date('G:i:s',$horaf);
	           //Fin
	    		$hora1           = strtotime(date_format($entity->getHorainicio(),'G:i:s'));
	    		$hora2           = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
	    		$idEC            = $entity->getEstudioCabina()->getId();
	    		$fechaelegida    =  strtotime($fecha);
	    		list($sdiaRegistro)= explode(':', date('w', $fechaelegida));
	            //Fin

               //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
	    		if($hora1 > $hora2){
	    			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
	    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
	    				'entity' => $entity,
	    				'edit_form'   => $editForm->createView(),
	    				'tipo' => $tipo,
                        'ubicacion'=> $ubicacion
	    				));
	    		}

	    		if($hora1 == $hora2){
	    			$this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
	    			return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
	    				'entity' => $entity,
	    				'edit_form'   => $editForm->createView(),
	    				'tipo' => $tipo,
                        'ubicacion'=> $ubicacion
	    				));
	    		}
			    //Realizo la consulta en base de dato 
		        //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE

		    		$pauta = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
		    		$pauta->setParameter(1, $horaini);
		    		$pauta->setParameter(2, $horafin);
		    		$pauta->setParameter(3, $idEC);
		    		$sqlpf = $pauta->getResult();
		                    //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA   
		    		$sqlp = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.estudioCabina =?3 and ( x.horainicio BETWEEN ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
		    		$sqlp->setParameter(1, $horaini);
		    		$sqlp->setParameter(2, $horafin);
		    		$sqlp->setParameter(3, $idEC);
		    		$spauta = $sqlp->getResult();

		        //Fin
		        //CONSULTO LAS RESERVACIONES PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
		    		$resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.id !='.$id.' and x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
		    		$resevaciones->setParameter(1, $horaini);
		    		$resevaciones->setParameter(2, $horafin);
		    		$resevaciones->setParameter(3, $idEC);
		    		$reserva = $resevaciones->getResult();

		        //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
		    		$resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.id !='.$id.' and x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
		    		$resevas->setParameter(1, $horaini);
		    		$resevas->setParameter(2, $horafin);
		    		$resevas->setParameter(3, $idEC);
		    		$reser = $resevas->getResult();
		         //FIN
		        //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA
		    		if($sqlpf){
		    			foreach ($sqlpf as $pautas) {

		    				$hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
		    				$hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
		    				$dias = $pautas->getDiasPautas();
		    				$arrayDay = explode(",", $dias);

		    				if (in_array($sdiaRegistro, $arrayDay)) {
		    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
		    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		    						'entity' => $entity,
		    						'edit_form'   => $editForm->createView(),
		    						'tipo' => $tipo,
                                    'ubicacion'=> $ubicacion
		    						));
		    				}
		    			}
		    		}

		    		if($spauta){
		    			foreach ($spauta as $pauts) {

		    				$hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
		    				$hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
		    				$dias = $pauts->getDiasPautas();
		    				$arrayDay = explode(",", $dias);

		    				if (in_array($sdiaRegistro, $arrayDay)) {

		    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
		    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		    						'entity' => $entity,
		    						'edit_form'   => $editForm->createView(),
		    						'tipo' => $tipo,
                                    'ubicacion'=> $ubicacion
		    						));
		    				}
		    			}
		    		}

		    		if($reser){
		    			foreach ($reser as $reservas){

		    				$hora1bdr        =  strtotime(date_format($reservas->getHorainicio(),'G:i:s'));
		    				$hora2bdr        =  strtotime(date_format($reservas->getHorafin(),'G:i:s'));
		    				$fechabd         =  strtotime(date_format($reservas->getFecha(),'Y-m-d'));
		    				$fechaelegida;

		    				if($fechaelegida == $fechabd){
		    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
		    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		    						'entity' => $entity,
		    						'edit_form'   => $editForm->createView(),
		    						'tipo' => $tipo,
                                    'ubicacion'=> $ubicacion
		    						));
		    				}
		    			} 
		    		}

		    		if($reserva){

		    			foreach ($reserva as $reservacion){
		    				$hora1bdr        =  strtotime(date_format($reservacion->getHorainicio(),'G:i:s'));
		    				$hora2bdr        =  strtotime(date_format($reservacion->getHorafin(),'G:i:s'));
		    				$fechabd         =  strtotime(date_format($reservacion->getFecha(),'Y-m-d'));
		    				$fechaelegida;

		    				if($fechaelegida == $fechabd){
		    					$this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
		    					return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
		    						'entity' => $entity,
		    						'edit_form'   => $editForm->createView(),
		    						'tipo' => $tipo,
                                    'ubicacion'=> $ubicacion
		    						));
		    				}
		    			}
		    		}

		    		$em->flush();

		    		$this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA EDITADO EXITOSAMENTE..');

		    		return $this->redirect($this->generateUrl('reservaciones_show',array(
                        'id' => $entity->getId(),
                        'ubicacion' => $ubicacion)));

	    	}
	    	return $this->render('EstudioCabinaBundle:Reservaciones:edit.html.twig', array(
	    						 'entity' 		=> $entity,
	    						 'edit_form'   	=> $editForm->createView(),
	    						 'tipo' 		=> $tipo,
                                 'ubicacion'    => $ubicacion
	    						));
    	}	
	}

    private function formatDateAction($date){

    	return \strtotime($date)*1000;
    }
    

    public function reservacionAction($id_selct,$tipo){

    	$em = $this->getDoctrine()->getManager();
    	/*Consulto las reservaciones*/
       //CONSULTO LAS RESERVACIONES A MOSTRAR EN EL CALENDARIO
    	$resevaciones = "select x from EstudioCabinaBundle:Reservaciones x where x.estudioCabina = $id_selct and x.estatus !=3";
    	$reser = $em->createQuery($resevaciones);
    	$sql = $reser->getResult();
        //FIN 
    	if($sql){
    		foreach($sql as $reservaciones){ 

    			$fechareservacion    = $reservaciones->getFecha();
    			$horaInicio          = $reservaciones->getHorainicio();
    			$horaFin             = $reservaciones->getHorafin();
             //TRANFORMO LA FECHA Y HORA EN FORMATO DATETIME
    			$fecha      = date_format($fechareservacion, 'Y-m-d');
    			$Hinicio    = date_format($horaInicio, 'G:i:s');
    			$Hfin       = date_format($horaFin, 'G:i:s');
             //FIN

             //CONCATENO Y CONVIERTO LA FECHA Y HORA EN FORMATO STRING PARA MOSTRARLO EN EL CALENDARIO
    			$strInicio    = $fecha." ".$Hinicio;
    			$strFin       = $fecha." ".$Hfin;
    			$horario      = $Hinicio." - ".$Hfin;
            //FIN
    			$actual         = strtotime(date_format(new \DateTime("now"), 'd-m-Y')) ;
    			$fechabd        = strtotime(date_format($fechareservacion, 'd-m-Y'));

    			if($actual > $fechabd and $reservaciones->getEstatus() != 1){
    				$reservaciones->setEstatus(4);

    			}elseif ($actual > $fechabd and $reservaciones->getEstatus()== 1) {
    				$reservaciones->setEstatus(5);

    			}

    			if($reservaciones->getEstatus()==1){
    				$class = "event-warning";
    			}elseif($reservaciones->getEstatus()==2){
    				$class = "event-success";
    			}elseif($reservaciones->getEstatus()==3){
    				$class = "event-important";
    			}elseif($reservaciones->getEstatus()== 4){
    				$class = "event";
    			}elseif($reservaciones->getEstatus()== 5){
    				$class = "event-inverse";
    			}

				$datos[]= array(
    				'id'                => $reservaciones->getId(),
    				'title'             => $reservaciones->getPauta().'/ Horario: '.$horario,
    				'class'             => $class,
    				'start'             => $this->formatDateAction($strInicio),
    				'end'               => $this->formatDateAction($strFin)

    				); 
    		}  
    	}

    //CONSULTO LOS DATOS A MOSTRAR EN EL CALENDARIO
    	$pautafijas= "select x from EstudioCabinaBundle:Pautafijas x where x.estudioCabina = $id_selct";
    	$query = $em->createQuery($pautafijas);
    	$pautafijasql = $query->getResult();
     //FIN 


    	foreach($pautafijasql as $pautas){ 
    		$phoraInicio          = $pautas->gethorainicio();
    		$phoraFin             = $pautas->gethorafin();
    		$dias                 = $pautas->getDiasPautas();
    		$arrayDay             = explode(",", $dias);
    		$fecha                = date_format($pautas->getFechaRegistro(), 'd-m-Y');
    		$fechaRegistro        = strtotime($fecha);
               #    Obtener los datos de la fecha separados
    		list($yearRegistro, $monthRegistro, $dayRegistro, $semanaRegistro, $sdiaRegistro) = explode(':', date('Y:m:d:W:w', $fechaRegistro));
    		$fechaLimite = strtotime('25-12-'.$yearRegistro);
    		list($yearLimite, $monthLimite, $dayLimite, $semanaLimite, $sdiaLimite) = explode(':', date('Y:m:d:W:w', $fechaLimite));

    		$difSemanas = $semanaLimite - $semanaRegistro;
               // $inicio = date('d-m-Y', $fechaRegistro);
    		$finlimite = date('d-m-Y', $fechaLimite);

    		for ($i=0; $i <= $difSemanas ; $i++) {
    			$fecha1semanasdespues = date('d-m-Y',strtotime('+'.$i.' weeks', $fechaRegistro));
    			list($year, $month, $day, $sday) = explode('-', date('Y-m-d-w', strtotime($fecha1semanasdespues)));
               	if($sday==0)
    				$sday=7;
    			$primerDia=date("d-m-Y",mktime(0,0,0,$month,$day-$sday+1,$year));
    			foreach ($arrayDay as $value) {
    				$Dia=date("d-m-Y",mktime(0,0,0,$month,$primerDia+$value-1,$year));
                    
    				$Dia=date("d-m-Y",mktime(0,0,0,$month,$primerDia+$value-1,$year));
                  //TRANFORMO LA FECHA Y HORA EN FORMATO DATETIME
    				$hInicio = date_format($phoraInicio,'G:i:s');
    				$hFin    = date_format($phoraFin,'G:i:s');
               //FIN
                //CONCATENO Y CONVIERTO LA FECHA Y HORA EN FORMATO STRING PARA MOSTRARLO EN EL CALENDARIO
    				$putaInicio      = $Dia." ".$hInicio;
    				$pautaFin        = $Dia." ".$hFin;
    				$pauinicio       = $this->formatDateAction($putaInicio);
    				$paufin          = $this->formatDateAction($pautaFin);
    				$horario          = $hInicio." - ".$hFin;
                 //FIN
                 #   Generar datos
    				$datos[]= array(
    					'id'                => $pautas->getId(),
    					'title'             => $pautas->getPauta().'/ Horario: '.$horario,
    					'class'             => "event-info",
    					'start'             => $pauinicio,
    					'end'               => $paufin

    					);
    			}
    		}
    	} 

       //MANDO LOS DATOS EN UN JSON
    	$response = new Response; 
    	$response->setContent(json_encode(array('success' => 1,'result' => $datos)));
    	return $response;
            //FIN
    }
    
    public function verificarFechaAction(Request $request){

    	$fechahoy = new \DateTime();
    	$fechaactual=  date_format($fechahoy, 'd-m-Y');
    	$actual = strtotime($fechaactual);  
    	$fecha_entrada = strtotime($request->request->get('fecha')).'/'; 

    	if($fecha_entrada < $actual){  
    		$response = new Response; 
    		$response->setContent(json_encode(array('valid' => 'no','mensaje' => 'La fecha debe ser mayor o igual a la actual.')));
    		return $response;
    	}else{ 
    		$response = new Response; 
    		$response->setContent(json_encode(array('valid' => 'si')));
    		return $response;
    	} 
    }
    
    public function actualizarestatusAction(Request $request, $tipoestatus,$id){


    	$em = $this->getDoctrine()->getManager();

    	$entity = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
        $entity = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
        $idsolicitante= $entity->getPerfilId();
        $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idsolicitante";
        $query = $em->createQuery($dql);
        $solicitante = $query->getResult();

    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Reservaciones entity.');
    	}
    	if($entity->getEstudioCabina()->getTipo()==2){

    		if($tipoestatus == 1){

    			$idPostProductor= $request->request->get('postProductor'); 
    			$postProductor = $em->getRepository('UsuarioBundle:Perfil')->find($idPostProductor);

    			$entity->setPostProductor($postProductor);
    			$entity->setEstatus(2);

    			$this->get('session')->getFlashBag()->add('notice', 'SOLICITUD HA SIDO APROBADA EXITOSAMENTE');
                //Creo mi objeto para guardar el historial de cuando se creo la solicitud
    			$historial       = new HistorialReservaciones();
                        //$idPerfil        = $entity->setPerfilId($idusuario);
    			$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
    			$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
    			$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
               //Seteo los datos para guardarlo
    			$historial ->setEstatus(2);
    			$historial->setPerfil($idusuario);
    			$historial->setReservaciones($idreservacion);
    			$historial->setFechaHora($fecha_operacion);

    			$em->persist($historial);
    			$em->flush();
                       
                //envio correo
                        $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
                        $query = $em->createQuery($dql);
                        $entities = $query->getResult();
                        $responsable=$entity->getPerfilId();
                        $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                        $correouser=$datouser->getUsername()."@telesurtv.net";
                        $subject= $entity->getEstudioCabina()->getNombre();
                                                    
                        if($entity->getEstudioCabina()->getTipo()==1){

                                $parametros['estudio']=$this->container->getParameter('estudio');
                                $estudio=explode(',',$parametros['estudio']);
                                $correoto = array_push($estudio,$correouser);
                                $correoto = $estudio; 
                        }elseif($entity->getEstudioCabina()->getTipo()== 2){
                                $parametros['cabina']=$this->container->getParameter('cabina');
                                $cabina=explode(',',$parametros['cabina']);
                                $correoto = array_push($cabina,$correouser);
                                $correoto = $cabina;
                        }elseif($entity->getEstudioCabina()->getTipo()== 3){
                            //Verifico que sala fue seleccionada para notificarle al coordinador 
                                if ($entity->getEstudioCabina()->getNombre()=='SALA DE PROYECCIONES'){
                                    $parametros['sproyecciones']=$this->container->getParameter('sproyecciones');
                                    $sala=explode(',',$parametros['sproyecciones']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #1'){
                                    $parametros['sala1']=$this->container->getParameter('sala1');
                                    $sala=explode(',',$parametros['sala1']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #4'){
                                    $parametros['sala4']=$this->container->getParameter('sala4');
                                    $sala=explode(',',$parametros['sala4']);
                                }

                                $correoto=array_push($sala,$correouser);
                            //Fin
                            $correoto = $sala;
                        //Envio correo a protocolo 
                        $query = $em->createQuery(
                                    'select x from EstudioCabinaBundle:Protocolo x join x.reservacion c
                                    WHERE c.id = :id'
                                    )->setParameter('id', $id);
                            $entity2 = $query->getResult();
                            $correoaplica="aplicaciones@telesurtv.net";
                            /*Correo responsables de protocolo*/
                                $parametros['protocolo']=$this->container->getParameter('protocolo');
                                $salapro=explode(',',$parametros['protocolo']);
                                $correoproto = array_push($salapro,$correouser);
                                $correoproto = $salapro;
                            /*Fin*/
                            $message = \Swift_Message::newInstance()   
                                ->setSubject('APROBADA RESERVACIÓN DE'.' '.$subject)  
                                ->setFrom($correoaplica)     // we configure the sender
                                ->setTo($correoproto)   
                                ->setBody( $this->renderView(
                                        'EstudioCabinaBundle:Correo:protocoloSalas.html.twig',
                                        array('sala'        => $entity2,
                                              'perfil'      => $entities,
                                              'solicitante' => $solicitante)
                                    ), 'text/html');

                             $this->get('mailer')->send($message);
                            
                        //Fin
                    }
                           
                            $correoaplica="aplicaciones@telesurtv.net";
                            $message = \Swift_Message::newInstance()   
                                ->setSubject('APROBADA RESERVACIÓN DE'.' '.$subject)  
                                ->setFrom($correoaplica)     // we configure the sender
                                ->setTo($correoto)   
                                ->setBody( $this->renderView(
                                        'EstudioCabinaBundle:Correo:aprobada.html.twig',
                                        array('reservaciones' => $entity,
                                              'perfil'        => $entities,
                                              'solicitante' => $solicitante)
                                    ), 'text/html');

                        $this->get('mailer')->send($message);   
           
            //fin enviar correo

                    }else{
                    	$observacionEstatus = $request->request->get('observacionEstatus'); 
                    	$entity->setObservacionEstatus($observacionEstatus);
                    	$entity->setEstatus(3);
                    	$this->get('session')->getFlashBag()->add('alert', 'SOLICITUD HA SIDO RECHAZADA EXITOSAMENTE');
                    //Creo mi objeto para guardar el historial de cuando se creo la solicitud
                    	$historial       = new HistorialReservaciones();
                        //$idPerfil        = $entity->setPerfilId($idusuario);
                    	$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
                    	$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
                    	$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
                   //Seteo los datos para guardarlo
                    	$historial ->setEstatus(2);
                    	$historial->setPerfil($idusuario);
                    	$historial->setReservaciones($idreservacion);
                    	$historial->setFechaHora($fecha_operacion);

                    	$em->persist($historial);
                    	$em->flush();
                       
                        //envio correo
                          $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
                            $query = $em->createQuery($dql);
                            $entities = $query->getResult();
                            $responsable=$entity->getPerfilId();
                            $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                            $correouser=$datouser->getUsername()."@telesurtv.net";
                            $subject= $entity->getEstudioCabina()->getNombre();
                            
                        if($entity->getEstudioCabina()->getTipo()==1){
                                $parametros['estudio']=$this->container->getParameter('estudio');
                                $estudio=explode(',',$parametros['estudio']);
                                $correoto = array_push($estudio,$correouser);
                                $correoto = $estudio; 
                        }elseif($entity->getEstudioCabina()->getTipo()== 2){
                                $parametros['cabina']=$this->container->getParameter('cabina');
                                $cabina=explode(',',$parametros['cabina']);
                                $correoto = array_push($cabina,$correouser);
                                $correoto = $cabina;
                        }elseif($entity->getEstudioCabina()->getTipo()== 3){
                            //Verifico que sala fue seleccionada para notificarle al coordinador 
                                if ($entity->getEstudioCabina()->getNombre()=='SALA DE PROYECCIONES'){
                                    $parametros['sproyecciones']=$this->container->getParameter('sproyecciones');
                                    $sala=explode(',',$parametros['sproyecciones']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #1'){
                                    $parametros['sala1']=$this->container->getParameter('sala1');
                                    $sala=explode(',',$parametros['sala1']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #4'){
                                    $parametros['sala4']=$this->container->getParameter('sala4');
                                    $sala=explode(',',$parametros['sala4']);
                                }

                                $correoto=array_push($sala,$correouser);
                            //Fin
                            $correoto = $sala;
                        }

                       $correoaplica="aplicaciones@telesurtv.net";
                       $message = \Swift_Message::newInstance()   
                        ->setSubject('RECHAZADA RESERVACIÓN DE'.' '.$subject)  
                        ->setFrom($correoaplica)     // we configure the sender
                        ->setTo($correoto)   
                        ->setBody( $this->renderView(
                                'EstudioCabinaBundle:Correo:rechazada.html.twig',
                                array('reservaciones' => $entity,
                                      'perfil'        => $entities,
                                      'solicitante' => $solicitante)
                            ), 'text/html');

                        $this->get('mailer')->send($message);   
            
                        //fin enviar correo
                    }
                }else{
                	if($tipoestatus == 1){
                		$entity->setEstatus(2);
                		$observacionEstatus = "SOLICITUD HA SIDO APROBADA EXITOSAMENTE"; 
                		$entity->setObservacionEstatus($observacionEstatus);
                		$this->get('session')->getFlashBag()->add('notice', 'SOLICITUD HA SIDO APROBADA EXITOSAMENTE');
                		$em->persist($entity);
                		$em->flush();

                    //Creo mi objeto para guardar el historial de cuando se creo la solicitud
                		$historial       = new HistorialReservaciones();
                		$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
                		$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
                		$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
                    //Seteo los datos para guardarlo
                		$historial ->setEstatus(2);
                		$historial->setPerfil($idusuario);
                		$historial->setReservaciones($idreservacion);
                		$historial->setFechaHora($fecha_operacion);

                		$em->persist($historial);
                		$em->flush();
                   
                 //envio correo
                          $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
                        $query = $em->createQuery($dql);
                        $entities = $query->getResult();
                        $responsable=$entity->getPerfilId();
                        $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                        $correouser=$datouser->getUsername()."@telesurtv.net";
                        $subject= $entity->getEstudioCabina()->getNombre();
                            
                        if($entity->getEstudioCabina()->getTipo()==1){
                                $parametros['estudio']=$this->container->getParameter('estudio');
                                $estudio=explode(',',$parametros['estudio']);
                                $correoto = array_push($estudio,$correouser);
                                $correoto = $estudio; 
                        }elseif($entity->getEstudioCabina()->getTipo()== 2){
                                $parametros['cabina']=$this->container->getParameter('cabina');
                                $cabina=explode(',',$parametros['cabina']);
                                $correoto = array_push($cabina,$correouser);
                                $correoto = $cabina;
                        }elseif($entity->getEstudioCabina()->getTipo()== 3){
                            //Verifico que sala fue seleccionada para notificarle al coordinador 
                                if ($entity->getEstudioCabina()->getNombre()=='SALA DE PROYECCIONES'){
                                    $parametros['sproyecciones']=$this->container->getParameter('sproyecciones');
                                    $sala=explode(',',$parametros['sproyecciones']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #1'){
                                    $parametros['sala1']=$this->container->getParameter('sala1');
                                    $sala=explode(',',$parametros['sala1']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #4'){
                                    $parametros['sala4']=$this->container->getParameter('sala4');
                                    $sala=explode(',',$parametros['sala4']);
                                }

                                $correoto=array_push($sala,$correouser);
                                
                            //Fin
                                $correoto = $sala;
                             //Envio correo a protocolo 
                                $query = $em->createQuery(
                                            'select x from EstudioCabinaBundle:Protocolo x join x.reservacion c
                                            WHERE c.id = :id'
                                            )->setParameter('id', $id);
                                    $entity2 = $query->getResult();
                                    $correoaplica="aplicaciones@telesurtv.net";
                                    /*Correo responsables de protocolo*/
                                        $parametros['protocolo']=$this->container->getParameter('protocolo');
                                        $salapro=explode(',',$parametros['protocolo']);
                                        $correoproto = array_push($salapro,$correouser);
                                        $correoproto = $salapro;
                                    /*Fin*/
                                    $message = \Swift_Message::newInstance()   
                                        ->setSubject('APROBADA RESERVACIÓN DE'.' '.$subject)  
                                        ->setFrom($correoaplica)     // we configure the sender
                                        ->setTo($correoproto)   
                                        ->setBody( $this->renderView(
                                                'EstudioCabinaBundle:Correo:protocoloSalas.html.twig',
                                                array('sala'        => $entity2,
                                                      'perfil'      => $entities,
                                                      'solicitante' => $solicitante)
                                            ), 'text/html');

                                     $this->get('mailer')->send($message);
                            //Fin
                        }

                        $correoaplica="aplicaciones@telesurtv.net";
                       $message = \Swift_Message::newInstance()   
                        ->setSubject('APROBADA RESERVACIÓN DE'.' '.$subject)  
                        ->setFrom($correoaplica)     // we configure the sender
                        ->setTo($correoto)   
                        ->setBody( $this->renderView(
                                'EstudioCabinaBundle:Correo:aprobada.html.twig',
                                array('reservaciones' => $entity,
                                      'perfil'      => $entities,
                                      'solicitante' => $solicitante)
                            ), 'text/html');

                        $this->get('mailer')->send($message);   
            
               //fin enviar correo

                        return $this->redirect($this->generateUrl('reservaciones_show',array(
                            'id' => $entity->getId(),
                            'ubicacion' => 0)));
                    }else{

                    	$observacionEstatus = $request->request->get('observacionEstatus'); 
                    	$entity->setObservacionEstatus($observacionEstatus);
                    	$entity->setEstatus(3);
                    	$this->get('session')->getFlashBag()->add('alert', 'SOLICITUD HA SIDO RECHAZADA EXITOSAMENTE');
                       //Creo mi objeto para guardar el historial de cuando se creo la solicitud
                    	$historial       = new HistorialReservaciones();
                        //$idPerfil        = $entity->setPerfilId($idusuario);
                    	$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
                    	$idreservacion   = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($entity->getId());
                    	$fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
                   //Seteo los datos para guardarlo
                    	$historial ->setEstatus(3);
                    	$historial->setPerfil($idusuario);
                    	$historial->setReservaciones($idreservacion);
                    	$historial->setFechaHora($fecha_operacion);

                    	$em->persist($historial);
                    	$em->flush();
            //envio correo
                        $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
                        $query = $em->createQuery($dql);
                        $entities = $query->getResult();
                        $responsable=$entity->getPerfilId();
                        $datouser=$em->getRepository('UsuarioBundle:User')->find($responsable);
                        $correouser=$datouser->getUsername()."@telesurtv.net";
                        $subject= $entity->getEstudioCabina()->getNombre();
                            
                        if($entity->getEstudioCabina()->getTipo()==1){
                                $parametros['estudio']=$this->container->getParameter('estudio');
                                $estudio=explode(',',$parametros['estudio']);
                                $correoto = array_push($estudio,$correouser);
                                $correoto = $estudio; 
                        }elseif($entity->getEstudioCabina()->getTipo()== 2){
                                $parametros['cabina']=$this->container->getParameter('cabina');
                                $cabina=explode(',',$parametros['cabina']);
                                $correoto = array_push($cabina,$correouser);
                                $correoto = $cabina;
                        }elseif($entity->getEstudioCabina()->getTipo()== 3){
                            //Verifico que sala fue seleccionada para notificarle al coordinador 
                                if ($entity->getEstudioCabina()->getNombre()=='SALA DE PROYECCIONES'){
                                    $parametros['sproyecciones']=$this->container->getParameter('sproyecciones');
                                    $sala=explode(',',$parametros['sproyecciones']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #1'){
                                    $parametros['sala1']=$this->container->getParameter('sala1');
                                    $sala=explode(',',$parametros['sala1']);
                                }elseif($entity->getEstudioCabina()->getNombre()=='SALA DE PISO #4'){
                                    $parametros['sala4']=$this->container->getParameter('sala4');
                                    $sala=explode(',',$parametros['sala4']);
                                }

                                $correoto=array_push($sala,$correouser);
                            //Fin
                            $correoto = $sala;
                        }

                        $correoaplica="aplicaciones@telesurtv.net";
                       $message = \Swift_Message::newInstance()   
                        ->setSubject('RECHAZADA RESERVACIÓN DE'.' '.$subject)  
                        ->setFrom($correoaplica)     // we configure the sender
                        ->setTo($correoto)   
                        ->setBody( $this->renderView(
                                'EstudioCabinaBundle:Correo:rechazada.html.twig',
                                array('reservaciones' => $entity,
                                      'perfil'      => $entities,
                                      'solicitante' => $solicitante)
                            ), 'text/html');

                        $this->get('mailer')->send($message);   
          
                        //fin enviar correo
                    }
                }

                $em->persist($entity);
                $em->flush();
                $response = new Response; 
                $response->setContent(json_encode(array('valid' => 'si')));
                return $response;
           // return $this->redirect($this->generateUrl('reservaciones_show',array('id' => $entity->getId())));


            }
        }