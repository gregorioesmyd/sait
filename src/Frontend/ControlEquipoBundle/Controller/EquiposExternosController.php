<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\EquiposExternos;
use Frontend\ControlEquipoBundle\Entity\RegistrosExternos;
use Frontend\ControlEquipoBundle\Form\EquiposExternosType;
use Frontend\ControlEquipoBundle\Form\RegistrosExternosType;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontendVisitasBundle\Entity\Usuario;
use Administracion\UsuarioBundle\Entity\Perfil;
/**
 * EquiposExternos controller.
 *
 */
class EquiposExternosController extends Controller
{

    /**
     * Lists all EquiposExternos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findAll();

        foreach ($entities as $entity)
        {
            $propietario        = $entity->getPropietarioId();
            $tipo_propietario   = $entity->getTipoPropietario();

            $usuarios[$entity->getId()] = $this->consultarUsuario($propietario, $tipo_propietario);

        }
        return $this->render('ControlEquipoBundle:EquiposExternos:index.html.twig', array(
            'entities' => $entities,
            'propietario' => $usuarios
        ));
    }

    /**
     * Consultar Usuario Visitante/Empleado.
     *
     */
    public function consultarUsuario ($id,$tipo)
    {
        $em = $this->getDoctrine()->getManager();
        if($tipo == 1) {
            // Consulto en la tabla usuario
            $usuarios = $em->getRepository('UsuarioBundle:Perfil')->find($id);
            return array(
                'id'        => $id,
                'nombres'   => strtoupper($usuarios->getPrimerNombre()) . ' ' . strtoupper($usuarios->getPrimerApellido()),
                'cedula'    => $usuarios->getCedula(),
                'tipo'      => 1);
        } else {
            // Consulto en la tabla visita
            $usuarios = $em->getRepository('ControlEquipoBundle:Usuario')->find($id);
            $nombres = explode(" ", $usuarios->getNombres());
            $apellidos = explode(" ", $usuarios->getApellidos());
            $cedula = explode(" ", $usuarios->getCedula());

            return array(
                'id'        => $id,
                'nombres'   => strtoupper($nombres[0]) . ' ' . strtoupper($apellidos[0]),
                'cedula'    => $cedula[0],
                'tipo'      => 2);
        }
    }

    public function limpiar($codigo) {

        $codigo = trim($codigo);
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $codigo = str_replace(
            array("\\", "¨", "º", "~", "--",
                 "#", "@", "|", "!", "\"",
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "`", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", "*", " ","¬","°","¸","="),
            '',
            $codigo
        );
        // Retorno String $codigo a UPPER y elimino
        return ltrim(rtrim(strtoupper($codigo), "-"), "-");;
    }
    /**
     * Creates a new EquiposExternos entity.
     *
     */
    public function createAction($visita = 0, Request $request)
    {
        $data = $request->get('controlequipobundle_equiposexternos');
        $entity = new EquiposExternos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if($this->getRequest()->isXmlHttpRequest()) {
                $validador = $this->get('validator');
                $errores = $validador->validate($entity);
                // Contamos los Errores de Validacion
                if (count($errores) > 0) {
                        $view = $this->renderView('ControlEquipoBundle:EquiposExternos:error.html.twig', array(
                            'form'   => $form->createView(),
                        ));
                        return new Response(json_encode(array(
                          'status'  => 'error',
                          'info'    => $view
                        )));
                }
                $creado = date_create_from_format('Y-m-d', \date("Y-m-d"));
                $entity->setCodigoBarra(trim(strtoupper($entity->getCodigoBarra())));
                $entity->setSerial(trim(strtoupper($entity->getSerial())));
                $entity->setCreado($creado);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $quipoExterno = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($entity->getId());
                    $propietarioId        = $quipoExterno->getPropietarioId();
                    $tipo_propietario     = $quipoExterno->getTipoPropietario();
                    // Consultamos los datos del Usuario
                    $usuario = $this->consultarUsuario($propietarioId,$tipo_propietario);
                    // Verificamos si es una visita para enviar la dependencia
                    // Objeto Registros Externos
                    $registroexterno = new RegistrosExternos();
                    $registroexterno->setPropietarioId($propietarioId);
                    $registroexterno->setTipo($tipo_propietario);
                    $registroexterno->setEquipo($quipoExterno);
                    $registroexterno->setEstatus(1);
                    // Si no es visita, obtenemos la dependencia
                    if($visita == 0) {
                      $usuariop = $em->getRepository('UsuarioBundle:Perfil')->find($propietarioId);
                      if (!$usuariop) {
                        $nivel = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find(67);
                        //$usuario->setNivelorganizacional($nivel);
                        $registroexterno->setDependencia($nivel);
                          //throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
                      } else {
                        //$nivel = $usuario->getNivelorganizacional();
                        $registroexterno->setDependencia($usuariop->getNivelorganizacional());
                      }
                      $dependencia = $registroexterno->getDependencia();
                    } else {
                      $dependencia = null;
                    }
                    $form_re = $this->createCreateFormRE($registroexterno);
                    $dql   = "SELECT r FROM ControlEquipoBundle:RegistrosExternos r where r.fechaSalida is null and r.equipo= :idequipo";
                    $query = $em->createQuery($dql);
                    $query->setParameter('idequipo',$quipoExterno->getId());
                    $estatus = $query->getResult();

                    $view = $this->renderView('ControlEquipoBundle:EquiposExternos:showresult.html.twig',
                            array('entity'      => $quipoExterno,
                                  'propietario' => $usuario,
                                  'form'        => $form_re->createView(),
                                  'visita'      => $visita,
                                  'ocupado'     => $estatus,
                                  'dependencia' => $dependencia,
                                  'creado'      => 1
                        ));
                    return new Response(json_encode(array(
                          'status' => 'ok',
                          'info' => $view
                        )));
        }
        // Sigue si la Peticion no es Ajax
        if ($form->isValid()) {
            // Guardamos la fecha actual
            $creado = date_create_from_format('Y-m-d', \date("Y-m-d"));
            $entity->setCodigoBarra(trim(strtoupper($entity->getCodigoBarra())));
            $entity->setSerial(trim(strtoupper($entity->getSerial())));
            $entity->setCreado($creado);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Equipo Externo registrado exitosamente.');
            return $this->redirect($this->generateUrl('equipoexterno_new'));
        }
        $propietario    =  $data['propietarioId'];
        $tipo           =   $data['tipoPropietario'];
        $usuario = $this->consultarUsuario($propietario, $tipo);

        return $this->render('ControlEquipoBundle:EquiposExternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'usuario' => $usuario
        ));
    }

    public function buscaAction()
    {
        return $this->render('ControlEquipoBundle:EquiposExternos:busca.html.twig',array());
    }

    public function busquedaDatosEmpleadoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cedula=$datos=$request->get('cedula');
        if(empty($cedula)){
            $this->get('session')->getFlashBag()->add('alert', 'La cédula no debe estar en blanco.');
            return $this->redirect($this->generateUrl('equipoexterno_search_empleado'));
        }

        //$usuario = $em->getRepository('UsuarioBundle:Perfil')->findByCedula($cedula);
        $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.cedula = '$cedula'";
        $usuario = $em->createQuery($dql)->getResult();
        //$usuario = $query;

        if(empty($usuario)){
            $this->get('session')->getFlashBag()->add('alert', 'La cédula no está registrada como empleado.');
            return $this->redirect($this->generateUrl('equipoexterno_search_empleado'));
        }

        return $this->redirect($this->generateUrl('equipoexterno_datos_empleado', array('id' => $usuario[0]->getId(),'tipo' => 1)));

    }

    public function datosEmpleadoAction($id,$tipo)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($id);
        if (!$usuario) {
            throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
        }
        if($usuario->getNivelorganizacional() == null) {
          $nivel = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find(67);
          $usuario->setNivelorganizacional($nivel);
        }
        $equipos  = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->findBy(array('propietarioId' => $id,
            'tipo' => $tipo,'fechaSalida' => null));
        $ye = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findBy(array('propietarioId' => $id,
            'tipoPropietario' => $tipo),array('id' => 'ASC'));

        return $this->render('ControlEquipoBundle:EquiposExternos:datos_empleado.html.twig', array(
            'entity' => $usuario,
            'equipos' => $equipos,
            'yourequipos' => $ye
        ));
    }

    /**
     * Creates a form to create a EquiposExternos entity.
     *
     * @param EquiposExternos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EquiposExternos $entity)
    {
        $form = $this->createForm(new EquiposExternosType(), $entity, array(
            //'action' => $this->generateUrl('equipoexterno_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Creates a form to create a RegistrosExternos entity.
     *
     * @param RegistrosExternos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFormRE(RegistrosExternos $entity)
    {
        $form = $this->createForm(new RegistrosExternosType(), $entity, array(
            'action' => '$this->generateUrl(equipoexterno_create)',
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Registrar Entrada'));

        return $form;
    }

    /**
     * Displays a form to create a new EquiposExternos entity.
     *
     */
    public function newAction($id,$tipo,Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Peticion Ajax
        if($this->getRequest()->isXmlHttpRequest()) {
            $datos          = $request->request->all();
            $codigo         = $this->limpiar($datos['codigoBarra']);
            // Vamos a validar el campo de codigo de barras

            //$codigo         = str_replace(' ','',$codigo);
            $propietarioId  = $datos['propietario'];
            $tipo           = $datos['tipo'];

            $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findOneByCodigoBarra($codigo);

            $usuario = $this->consultarUsuario($propietarioId,$tipo);
            if($tipo == 2) {
                $visita     = $datos['visita'];
            } else  $visita = 0;
            if(!empty($codigoequipo)) {
                if($visita == 0) {
                  $user = $em->getRepository('UsuarioBundle:Perfil')->find($propietarioId);
                  if($user->getNivelorganizacional() == null) {
                    $nivel = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find(67);
                    $user->setNivelorganizacional($nivel);
                    $dependencia = $user->getNivelorganizacional();
                  } else {
                    $dependencia = $user->getNivelorganizacional();
                  }
                } else {
                  $dependencia = null;
                }
                if(($codigoequipo->getPropietarioId() == $propietarioId) and ($tipo ==  $codigoequipo->getTipoPropietario())) {
                    $equipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($codigoequipo->getId());
                    $registroexterno = new RegistrosExternos();
                    $registroexterno->setPropietarioId($propietarioId);
                    $registroexterno->setTipo($tipo);
                    $registroexterno->setEquipo($equipo);
                    // Vamos a consultar la dependencia del usuario
                    if($visita == 0 and $tipo = 1) {
                      $registroexterno->setDependencia($dependencia);
                      $dependencia = $registroexterno->getDependencia();
                    }
                    $form_re = $this->createCreateFormRE($registroexterno);

                    $dql   = "SELECT r FROM ControlEquipoBundle:RegistrosExternos r where r.fechaSalida is null and r.equipo= :idequipo";
                    $query = $em->createQuery($dql);
                    $query->setParameter('idequipo',$equipo->getId());
                    //$estatus = $query->getResult();
                    $estatus = $query->getOneOrNullResult();
                    if(!is_null($estatus)) {
                      $estatus = $estatus->getId();
                    }
                    return $this->render('ControlEquipoBundle:EquiposExternos:showresult.html.twig', array(
                        'entity'      => $equipo,
                        'dependencia' => $dependencia,
                        'propietario' => $usuario,
                        'visita'      => $visita,
                        'form'        => $form_re->createView(),
                        'ocupado'     => $estatus
                    ));
                } else {
                    //$propietarioId = 0;
                    //$tipo = 0;
                    $propietario = $this->consultarUsuario($codigoequipo->getPropietarioId(),$codigoequipo->getTipoPropietario());
                    return $this->render('ControlEquipoBundle:EquiposExternos:showresult.html.twig', array(
                        'entity'      => $codigoequipo,
                        'propietario' => $propietario,
                        'visita'      => $visita,
                        'dependencia' => $dependencia,
                        //'form'        => $form_re->createView(),
                        'ocupado'     => 0
                    ));
                }
            } else {
                $entity = new EquiposExternos();
                $entity->setCodigoBarra($codigo);
                $form   = $this->createCreateForm($entity);

                $view = $this->renderView('ControlEquipoBundle:EquiposExternos:new_equipo.html.twig', array(
                        'entity'        => $entity,
                        'propietario'   => $usuario,
                        'visita'        => $visita,
                        'form'          => $form->createView(),
                        ));
                return new Response($view);
            }

        }
        // Finaliza Peticion Ajax

        $entity = new EquiposExternos();
        $form   = $this->createCreateForm($entity);

        // Ahora buscamos al Propietario
        $usuario = $this->consultarUsuario($id, $tipo);

            return $this->render('ControlEquipoBundle:EquiposExternos:new.html.twig', array(
                    'entity' => $entity,
                    'usuario'=> $usuario,
                    'form'  => $form->createView(),
                    ));
    }

     /**
     * Displays a form to create a new EquiposExternos entity.
     *
     */
    public function registrarSalidaAction($id,$tipo,Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // Peticion Ajax
        if($this->getRequest()->isXmlHttpRequest()) {
            $datos          = $request->request->all();
            $codigo         = $datos['codigoBarra'];
            $propietarioId  = $datos['propietario'];
            $tipo           = $datos['tipo'];

            $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findOneByCodigoBarra($codigo);

            $usuario = $this->consultarUsuario($propietarioId,$tipo);
            if($tipo == 2) {
                $visita     = $datos['visita'];
            } else $visita = 0;

            if(!empty($codigoequipo)) {
                if(($codigoequipo->getPropietarioId() == $propietarioId) and ($tipo ==  $codigoequipo->getTipoPropietario())) {
                    $equipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($codigoequipo->getId());
                    $dql   = "SELECT r FROM ControlEquipoBundle:RegistrosExternos r where r.fechaSalida is null and r.equipo= :idequipo";
                    $query = $em->createQuery($dql);
                    $query->setParameter('idequipo',$equipo->getId());
                    $estatus = $query->getOneOrNullResult();
                    if(!is_null($estatus)) {
                      $dependencia = $estatus->getDependencia();
                      $estatus     = $estatus->getId();
                    } else {
                      $dependencia = null;
                    }
                    return $this->render('ControlEquipoBundle:EquiposExternos:showresultsalida.html.twig', array(
                            'entity'      => $equipo,
                            'propietario' => $usuario,
                            'visita'      => $visita,
                            'ocupado'     => $estatus,
                            'dependencia' => $dependencia
                    ));
                } else {
                    $propietario = $this->consultarUsuario($codigoequipo->getPropietarioId(),$codigoequipo->getTipoPropietario());
                    return $this->render('ControlEquipoBundle:EquiposExternos:showresultsalida.html.twig', array(
                        'entity'      => $codigoequipo,
                        'propietario' => $propietario,
                        'visita'      => $visita,
                        //'form'        => $form_re->createView(),
                        'ocupado'     => 0
                    ));
                }

            } else {
                return new Response('<div class="alert alert-danger"><center>Equipo No Encontrado!</center></div>');
            }

        }
    }
    /**
     * Finds and displays a EquiposExternos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
        }

        $propietario        = $entity->getPropietarioId();
        $tipo_propietario   = $entity->getTipoPropietario();
        $usuario = $this->consultarUsuario($propietario, $tipo_propietario);
        //$creado =
        $queryDql = $em->createQuery('SELECT re
                                    FROM ControlEquipoBundle:RegistrosExternos re
                                    WHERE re.equipo = :equipo
                                    ORDER BY re.id ASC'
                                )->setParameter('equipo',$entity->getId())
                                ->setMaxResults(1);
        $result  = $queryDql->getResult();
        if(!empty($result)) {
          $query3 = $em->createQuery('SELECT oe
                                      FROM ControlEquipoBundle:OperacionesExternos oe
                                      WHERE oe.tipoOperacion = 1
                                      AND oe.registroEquipoExterno = :registro
                                      ORDER BY oe.id ASC'
                                  )->setParameter('registro',$result[0]->getId())->setMaxResults(1);
          $result  = $query3->getResult();
          $creado = $result[0]->getReponsableUsuario();
        } else {
          $creado = 'NO DISPONIBLE';
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:EquiposExternos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'propietario' => $usuario,
            'creador'     => $creado
        ));
    }

    /**
     * Finds and displays a EquiposExternos entity.
     *
     */
    public function rastreoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
        }

        $propietario        = $entity->getPropietarioId();
        $tipo_propietario   = $entity->getTipoPropietario();
        $usuario = $this->consultarUsuario($propietario, $tipo_propietario);
        // Buscamos en la Tabla RegistrosExternos
        $dql   = "SELECT r FROM ControlEquipoBundle:RegistrosExternos r where r.equipo= :idequipo";
        $query = $em->createQuery($dql);
        $query->setParameter('idequipo',$id);
        $registroequipo = $query->getResult();

            if(!empty($registroequipo)) {
            // Vamos a verificar las operaciones
            $operacionarray= array();
            $query_operaciones = $em->createQuery('SELECT o
                                    FROM ControlEquipoBundle:OperacionesExternos o
                                    WHERE o.registroEquipoExterno = :regexterno'
                                )->setParameter('regexterno',$registroequipo[0]->getId());
            $operaciones  = $query_operaciones->getResult();

                foreach ($operaciones as $operacion) {
                    $operacionarray[$operacion->getTipoOperacion()] = $operacion->getReponsableUsuario();
                }
            } else {
                $operacionarray= array();
            }

        return $this->render('ControlEquipoBundle:EquiposExternos:rastreo.html.twig', array(
            'entity'            => $entity,
            'registroequipo'    => $registroequipo,
            'propietario'       => $usuario,
            'operaciones'       => $operacionarray
        ));
    }

    /**
     * Displays a form to edit an existing EquiposExternos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
        }
        $entity->setCodigoBarra(strtoupper($entity->getCodigoBarra()));
        $entity->setSerial(strtoupper($entity->getSerial()));
        $propietario        = $entity->getPropietarioId();
        $tipo_propietario   = $entity->getTipoPropietario();
        $usuario            = $this->consultarUsuario($propietario, $tipo_propietario);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:EquiposExternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'propietario'  => $usuario
        ));
    }

    /**
    * Creates a form to edit a EquiposExternos entity.
    *
    * @param EquiposExternos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EquiposExternos $entity)
    {
        $form = $this->createForm(new EquiposExternosType(), $entity, array(
            'action' => $this->generateUrl('equipoexterno_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modificar'));

        return $form;
    }

    /**
     * Edits an existing EquiposExternos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
        }
        $entity->setCodigoBarra(strtoupper($entity->getCodigoBarra()));
        $entity->setSerial(strtoupper($entity->getSerial()));
        $propietario        = $entity->getPropietarioId();
        $tipo_propietario   = $entity->getTipoPropietario();
        $usuario            = $this->consultarUsuario($propietario, $tipo_propietario);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $entity->setCodigoBarra(strtoupper($entity->getCodigoBarra()));
        $entity->setSerial(strtoupper($entity->getSerial()));

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Equipo Externo modificado exitosamente.');
            return $this->redirect($this->generateUrl('equipoexterno'));
        }

        return $this->render('ControlEquipoBundle:EquiposExternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'propietario'  => $usuario
        ));
    }
    /**
     * Deletes a EquiposExternos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EquiposExternos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('equipoexterno'));
    }

    /**
     * Creates a form to delete a EquiposExternos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipoexterno_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
}
