<?php

namespace Frontend\EstudioCabinaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\EstudioCabinaBundle\Entity\Pautafijas;
use Frontend\EstudioCabinaBundle\Form\PautafijasType;

/**
 * Pautafijas controller.
 *
 */
class PautafijasController extends Controller {

    /**
     * Lists all Pautafijas entities.
     *
     */
    public function indexAction($tipo) {

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
                        'select x from EstudioCabinaBundle:Pautafijas x join x.estudioCabina c
                          WHERE c.tipo = :tipo'
                )->setParameter('tipo', $tipo);
        $entities = $query->getResult();
        if ($entities) {
            foreach ($entities as $entity) {
                $dias = explode(",", $entity->getDiasPautas());
                $diassemana[$entity->getId()] = $dias;
            }
        } else {
            $diassemana = array();
        }



        return $this->render('EstudioCabinaBundle:Pautafijas:index.html.twig', array(
                    'entities' => $entities,
                    'tipo' => $tipo,
                    'diassem' => $diassemana
        ));
    }

    /**
     * Creates a new Pautafijas entity.
     *
     */
    public function createAction(Request $request, $tipo) {
        $entity = new Pautafijas();
        $form = $this->createCreateForm($tipo, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            //Datos capturado en el formulario
            //resto un minuto a la hora de inicio y fin
            $diasElegidos = $entity->getDiasPautas();
            $horai = strtotime('+1 minute', strtotime(date_format($entity->getHorainicio(), 'G:i:s')));
            $horaf = strtotime('-1 minute', strtotime(date_format($entity->getHorafin(), 'G:i:s')));
            $horaini = date('G:i:s', $horai);
            $horafin = date('G:i:s', $horaf);

            $fecha = date('Y-m-d');



            //Fin
            $hora1 = strtotime(date_format($entity->getHorainicio(), 'G:i:s'));
            $hora2 = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
            $idEC = $entity->getEstudioCabina()->getId();
            //Fin
            //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
            if ($hora1 > $hora2) {
                $this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                            'entity' => $entity,
                            'form' => $form->createView(),
                            'tipo' => $tipo,
                ));
            }

            if ($hora1 == $hora2) {
                $this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                            'entity' => $entity,
                            'form' => $form->createView(),
                            'tipo' => $tipo,
                ));
            }
            //Realizo la consulta en base de dato 
            //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
            $em = $this->getDoctrine()->getManager();
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
            $resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin) and x.fecha=?4');
            $resevaciones->setParameter(1, $horaini);
            $resevaciones->setParameter(2, $horafin);
            $resevaciones->setParameter(3, $idEC);
            $resevaciones->setParameter(4, $fecha);
            $reserva = $resevaciones->getResult();
            //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
            $resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE  x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2) and x.fecha=?4');
            $resevas->setParameter(1, $horaini);
            $resevas->setParameter(2, $horafin);
            $resevas->setParameter(3, $idEC);
            $resevas->setParameter(4, $fecha);
            $reser = $resevas->getResult();


            //FIN
            //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA.

            if (count($sqlpf) > 0) {

                foreach ($sqlpf as $pautas) {

                    $hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
                    $hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
                    $dias = $pautas->getDiasPautas();
                    $arrayDay = explode(",", $dias);
                    foreach ($diasElegidos as $value) {
                        if (in_array($value, $arrayDay)) {

                            $this->get('session')->getFlashBag()->add('alert', 'ERROR 801:Este horario esta ocupado por otra pauta fija..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                                        'entity' => $entity,
                                        'form' => $form->createView(),
                                        'tipo' => $tipo,
                            ));
                        }
                    }
                }
            }
            if (count($spauta)> 0) {

                foreach ($spauta as $pauts) {

                    $hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
                    $hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
                    $dias = $pauts->getDiasPautas();
                    $arrayDay = explode(",", $dias);
                    
                    foreach ($diasElegidos as $value) {
                        if (in_array($value, $arrayDay)) {

                            $this->get('session')->getFlashBag()->add('alert', 'ERROR 802: Este horario esta ocupado por otra pauta fija..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                                        'entity' => $entity,
                                        'form' => $form->createView(),
                                        'tipo' => $tipo,
                            ));
                        }
                    }
                }
            }

            if (count($reser)>0) {
                
                foreach ($reser as $reservas) {

                    $hora1bdr   = strtotime(date_format($reservas->getHorainicio(), 'G:i:s'));
                    $hora2bdr   = strtotime(date_format($reservas->getHorafin(), 'G:i:s'));
                    $fecha      = strtotime(date_format($reservas->getFecha(), 'Y-m-d'));
                    $fecha_det  = explode(':', date('w',$fecha)); 
                    
                  foreach ($diasElegidos as $value) {
                      
                        if (in_array($value, $fecha_det)) {

                            $this->get('session')->getFlashBag()->add('alert', 'ERROR 803: Este horario esta ocupado por una reservación..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                                        'entity' => $entity,
                                        'form' => $form->createView(),
                                        'tipo' => $tipo,
                            ));
                        }
                    }
                }
            }

            if (count($reserva)>0) {
               
                foreach ($reserva as $reservacion) {
                    
                    $hora1bdr = strtotime(date_format($reservacion->getHorainicio(), 'G:i:s'));
                    $hora2bdr = strtotime(date_format($reservacion->getHorafin(), 'G:i:s'));
                    $fecha      = strtotime(date_format($reservacion->getFecha(), 'Y-m-d'));
                    $fecha_det  = explode(':', date('w',$fecha)); 

                    foreach ($diasElegidos as $value) {

                        if (in_array($value, $fecha_det)) {

                            $this->get('session')->getFlashBag()->add('alert', 'ERROR 804: Este horario esta ocupado por una reservación2..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                                        'entity' => $entity,
                                        'form' => $form->createView(),
                                        'tipo' => $tipo,
                            ));
                        }
                    }
                }
            }

            $diasemana = '';
            $diasPautas = array_filter($entity->getDiasPautas(), 'strlen');
            $c = count($diasPautas);
            foreach ($diasPautas as $dias) {
                if ($c > 1) {
                    $diasemana .= $dias . ',';
                } else {
                    $diasemana .= $dias;
                }
                --$c;
            }

            $fechahoy = new \DateTime();
            $entity->setFechaRegistro($fechahoy);
            $entity->setDiasPautas($diasemana);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA REALIZADO EXITOSAMENTE');
            return $this->redirect($this->generateUrl('pautafijas_show', array('id' => $entity->getId())));
        }

        return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'tipo' => $tipo,
        ));
    }

    /**
     * Creates a form to create a Pautafijas entity.
     *
     * @param Pautafijas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($tipo, Pautafijas $entity) {
        $form = $this->createForm(new PautafijasType($tipo), $entity, array(
            'action' => $this->generateUrl('pautafijas_create', array('tipo' => $tipo)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pautafijas entity.
     *
     */
    public function newAction($tipo) {
        $entity = new Pautafijas();
        $form = $this->createForm(new PautafijasType($tipo), $entity);

        return $this->render('EstudioCabinaBundle:Pautafijas:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'tipo' => $tipo
        ));
    }

    /**
     * Finds and displays a Pautafijas entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:Pautafijas')->find($id);
        $tipo = $entity->getEstudioCabina()->getTipo();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautafijas entity.');
        }
        $dias = explode(",", $entity->getDiasPautas());
        $entity->setDiasPautas($dias);

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:Pautafijas:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
                    'tipo' => $tipo,
        ));
    }

    /**
     * Displays a form to edit an existing Programas entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:Pautafijas')->find($id);
        $tipo = $entity->getEstudioCabina()->getTipo();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautafijas entity.');
        }

        $diasPautas = $entity->getDiasPautas();
        $day = explode(",", $diasPautas);

        $entity->setDiasPautas($day);
        $editForm = $this->createEditForm($tipo, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'tipo' => $tipo,
        ));
    }

    /**
     * Creates a form to edit a Pautafijas entity.
     *
     * @param Pautafijas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($tipo, Pautafijas $entity) {
        $form = $this->createForm(new PautafijasType($tipo), $entity, array(
            'action' => $this->generateUrl('pautafijas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Pautafijas entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('EstudioCabinaBundle:Pautafijas')->find($id);
        $tipo = $entity->getEstudioCabina()->getTipo();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautafijas entity.');
        }
        $data = $request->request->all();
        $diasPautas = $data['frontend_estudiocabinabundle_pautafijas']['diasPautas'];
        $entity->setDiasPautas($diasPautas);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($tipo, $entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {

            //Datos capturado en el formulario
            //resto un minuto a la hora de inicio y fin
            $diasElegidos = $entity->getDiasPautas();
            $horai = strtotime('+1 minute', strtotime(date_format($entity->getHorainicio(), 'G:i:s')));
            $horaf = strtotime('-1 minute', strtotime(date_format($entity->getHorafin(), 'G:i:s')));
            $horaini = date('G:i:s', $horai);
            $horafin = date('G:i:s', $horaf);
            //Fin
            $hora1 = strtotime(date_format($entity->getHorainicio(), 'G:i:s'));
            $hora2 = strtotime(date_format($entity->getHorafin(), 'G:i:s'));
            $idEC = $entity->getEstudioCabina()->getId();
            //Fin
            //VALIDO QUE LA HORA DE INICIO SEA MAYOR QUE LA HORA FIN
            if ($hora1 > $hora2) {
                $this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                        'entity' => $entity,
                                        'edit_form' => $editForm->createView(),
                                        'delete_form' => $deleteForm->createView(),
                                        'tipo' => $tipo,
                            ));
            }

            if ($hora1 == $hora2) {
                $this->get('session')->getFlashBag()->add('alert', 'La Hora de fin debe de ser mayor que la hora inicio ..');
                return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                    'entity' => $entity,
                                    'edit_form' => $editForm->createView(),
                                    'delete_form' => $deleteForm->createView(),
                                    'tipo' => $tipo,
                        ));
            }
            //Realizo la consulta en base de dato 
            //CONSULTO LAS PAUTAS FIJAS PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
            
            $pauta = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.id !='."'$id'".' and x.estudioCabina =?3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
            $pauta->setParameter(1, $horaini);
            $pauta->setParameter(2, $horafin);
            $pauta->setParameter(3, $idEC);
            $sqlpf = $pauta->getResult();
            //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA   
            $sqlp = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Pautafijas x  WHERE x.id !='."'$id'".' and  x.estudioCabina =?3 and ( x.horainicio BETWEEN ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
            $sqlp->setParameter(1, $horaini);
            $sqlp->setParameter(2, $horafin);
            $sqlp->setParameter(3, $idEC);
            $spauta = $sqlp->getResult();

            //Fin
            //CONSULTO LAS RESERVACIONES PARA VALIDAR QUE NO CHOQUEN LAS HORAS CUANDO RESERVE
            $resevaciones = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE x.estudioCabina =?3 and x.estatus !=3 and ( ?1 BETWEEN x.horainicio AND x.horafin or ?2 BETWEEN x.horainicio AND x.horafin)');
            $resevaciones->setParameter(1, $horaini);
            $resevaciones->setParameter(2, $horafin);
            $resevaciones->setParameter(3, $idEC);
            $reserva = $resevaciones->getResult();
            //CONSULTO SI LA HORA QUE ESTA EN BASE DE DATO ESTA DENTRO DE LA HORA QUE FUE ELEGIDA
            $resevas = $em->createQuery('SELECT x FROM EstudioCabinaBundle:Reservaciones x  WHERE x.estudioCabina =?3 and x.estatus !=3 and ( x.horainicio  BETWEEN  ?1 AND ?2 or x.horafin BETWEEN ?1 AND ?2)');
            $resevas->setParameter(1, $horaini);
            $resevas->setParameter(2, $horafin);
            $resevas->setParameter(3, $idEC);
            $reser = $resevas->getResult();
            //FIN
            //VALIDO QUE EL HORARIO ELEGIDO POR EL USUARIO NO ESTE OCUPADO POR UNA PAUTA FIJA.

            if ($sqlpf) {

                foreach ($sqlpf as $pautas) {

                    $hora1bd = strtotime(date_format($pautas->getHorainicio(), 'G:i:s'));
                    $hora2bd = strtotime(date_format($pautas->getHorafin(), 'G:i:s'));
                    $dias = $pautas->getDiasPautas();
                    $arrayDay = explode(",", $dias);
                    foreach ($diasElegidos as $value) {
                        if (in_array($value, $arrayDay)) {

                            $this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                                'entity' => $entity,
                                                'edit_form' => $editForm->createView(),
                                                'delete_form' => $deleteForm->createView(),
                                                'tipo' => $tipo,
                                    ));
                        }
                    }
                }
            }
            if ($spauta) {

                foreach ($spauta as $pauts) {

                    $hora1bd = strtotime(date_format($pauts->getHorainicio(), 'G:i:s'));
                    $hora2bd = strtotime(date_format($pauts->getHorafin(), 'G:i:s'));
                    $dias = $pauts->getDiasPautas();
                    $arrayDay = explode(",", $dias);
                    
                    foreach ($diasElegidos as $value) {
                        if (in_array($value, $arrayDay)) {

                            $this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                                                    return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                            'entity' => $entity,
                                            'edit_form' => $editForm->createView(),
                                            'delete_form' => $deleteForm->createView(),
                                            'tipo' => $tipo,
                                ));
                        }
                    }
                }
            }

            if ($reser) {
                
                foreach ($reser as $reservas) {

                    $hora1bdr   = strtotime(date_format($reservas->getHorainicio(), 'G:i:s'));
                    $hora2bdr   = strtotime(date_format($reservas->getHorafin(), 'G:i:s'));
                    $fecha      = strtotime(date_format($reservas->getFecha(), 'Y-m-d'));
                    $fecha_det  = explode(':', date('w',$fecha)); 
                    
                  foreach ($diasElegidos as $value) {
                      
                        if (in_array($value, $fecha_det)) {

                            $this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                                'entity' => $entity,
                                                'edit_form' => $editForm->createView(),
                                                'delete_form' => $deleteForm->createView(),
                                                'tipo' => $tipo,
                                    ));
                        }
                    }
                }
            }

            if ($reserva) {
               
                foreach ($reserva as $reservacion) {
                    
                    $hora1bdr = strtotime(date_format($reservacion->getHorainicio(), 'G:i:s'));
                    $hora2bdr = strtotime(date_format($reservacion->getHorafin(), 'G:i:s'));
                    $fecha      = strtotime(date_format($reservacion->getFecha(), 'Y-m-d'));
                    $fecha_det  = explode(':', date('w',$fecha)); 

                    foreach ($diasElegidos as $value) {

                        if (in_array($value, $fecha_det)) {

                            $this->get('session')->getFlashBag()->add('alert', 'Debe elegir un horario que no este ocupado..');
                            return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                                            'entity' => $entity,
                                            'edit_form' => $editForm->createView(),
                                            'delete_form' => $deleteForm->createView(),
                                            'tipo' => $tipo,
                                ));
                        }
                    }
                }
            }


            $diasemana = '';
            $diasP = array_filter($diasPautas, 'strlen');
            $c = count($diasP);
            
            foreach ($diasP as $dias) {
                if ($c > 1) {
                    $diasemana .= $dias . ',';
                } else {
                    $diasemana .= $dias;
                }
                --$c;
            }

            $entity->setDiasPautas($diasemana);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA EDITADO EXITOSAMENTE..');

            return $this->redirect($this->generateUrl('pautafijas_show', array('id' => $entity->getId())));
        }


        return $this->render('EstudioCabinaBundle:Pautafijas:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Programas entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EstudioCabinaBundle:Pautafijas')->find($id);
            $tipo = $entity->getEstudioCabina()->getTipo();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pautafijas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pautafijas', array('tipo' => $tipo)));
    }

    /**
     * Creates a form to delete a Pautafijas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('pautafijas_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
