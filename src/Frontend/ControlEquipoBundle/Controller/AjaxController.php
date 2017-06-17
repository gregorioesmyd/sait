<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontendVisitasBundle\Entity\Usuario;
use Administracion\UsuarioBundle\Entity\Perfil;
use Frontend\ControlEquipoBundle\Entity\EquiposInternos;
use Frontend\ControlEquipoBundle\Entity\EquiposPautas;
use Frontend\ControlEquipoBundle\Entity\Pautas;
use Frontend\ControlEquipoBundle\Entity\Modelos;
use Frontend\ControlEquipoBundle\Entity\Marcas;
use Frontend\ControlEquipoBundle\Entity\TiposOperaciones;


/**
 * Categoria controller.
 *
 */
class AjaxController extends Controller
{

    /*
     * Obtiene los modelos de una marca en especifico
     */
    public function modelosAction(Request $request)
    {
        $marca_id = $request->request->get('marca_id');
        $em = $this->getDoctrine()->getManager();
        $modelos = $em->getRepository('ControlEquipoBundle:Modelos')->findByMarcaId($marca_id);
        return new JsonResponse($modelos);
    }

    /*
     * Obtiene el Responsable de una Visita o de Usuario
     */
    public function busquedaResponsableAction(Request $request)
    {
        // Para loa consulta del Usuario / Visitante
        $cedula = $request->request->get('cedula');

        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('UsuarioBundle:Perfil')->findOneByCedula($cedula);

            //var_dump($usuarios);
            //die;
        //return new JsonResponse($modelos);
        if(!empty($usuarios)) {
            $usuario = array(
                'primer_nombre' => $usuarios->getPrimerNombre(),
                'primer_apellido' => $usuarios->getPrimerApellido(),
                'tipo_propietario' => 1,
                'propietario' => $usuarios->getUser()->getId(),
                'dependencia' => $usuarios->getUser()->getNivelorganizacional(),
                'vacio' => 'no');
            return new JsonResponse($usuario);
        } else {
            $em = $this->getDoctrine()->getManager();
            $usuarios = $em->getRepository('FrontendVisitasBundle:Usuario')->findOneByCedula($cedula);

            if(!empty($usuarios)) {
                $nombres = explode(" ", $usuarios->getNombres());
                $apellidos = explode(" ", $usuarios->getApellidos());
                $usuario = array(
                    'primer_nombre' => \strtoupper($nombres[0]),
                    'primer_apellido' => \strtoupper($apellidos[0]),
                    'tipo_propietario' => 2,
                    'dependencia' => null,
                    'propietario' => $usuarios->getId(),
                    'vacio' => 'no');
                return new JsonResponse($usuario);
            }
            $var = array('vacio' => null);
            return new JsonResponse($var);
        }
    }

    /*
     * Obtiene el Equipo por el codigo de Barra
     */
    public function busquedaEquipoAction(Request $request) {
        $codigo   = strtoupper($request->request->get('codigoBarra'));
        $pauta_id = $request->request->get('pauta');
        $em = $this->getDoctrine()->getManager();
        $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposInternos')->findOneByCodigoBarra($codigo);
        if(!empty($codigoequipo)) {
            $pauta       = $em->getRepository('ControlEquipoBundle:Pautas')->find($pauta_id); // Obtienes la Pauta
            $equipo      = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($codigoequipo->getId());

            $equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findBy(
                    array('pauta' => $pauta,'equipoInterno' => $equipo));
            if(empty($equipos_pautas)) {
                // No existe, lo agregamos a la lista
                // Pero antes verificamos si el equipo esta disponible
                if($equipo->getEstatus() == 1) {
                    // Esta disponible
                    $equipopauta = new EquiposPautas();
                    // Seteamos los valores de la Operacion
                    $equipopauta->setPauta($pauta);
                    $equipopauta->setEquipoInterno($equipo);
                    if($pauta->getTipoPauta() == 2) {
                        $equipopauta->setChequeoSalida(TRUE);
                    } else {
                        $equipopauta->setChequeoSalida(FALSE);
                    }
                    $equipopauta->setChequeoEntrada(FALSE);
                    //$equipopauta->setEquipoInterno($equipo);
                    // Vamos a guardar los datos del equipo en la tabla Operaciones
                    $em->persist($equipopauta);
                    $em->flush();


                    $equipo_array = array(
                            'pauta'         => $equipopauta->getId(),
                            'descripcion'   => $codigoequipo->getDescripcionEquipo(),
                            'equipo'        => $codigoequipo->getId(),
                            'fotoReferencia'=> $codigoequipo->getFotoReferencia(),
                            'vacio'         => 'no');
                    // Actualizamos el estatus del equipo
                    $equiposinternos = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($codigoequipo->getId());
                    if (!$equiposinternos) {
                        //throw $this->createNotFoundException('Unable to find EquiposPautas entity.');
                        $var = array('mensaje' => 'Error al agregar equipo a la pauta.');
                        return new JsonResponse($var);
                    }
                    $equiposinternos->setEstatus(2);
                    $em->persist($equiposinternos);
                    $em->flush();
                    return new JsonResponse($equipo_array);
                } else {
                    $equipo_json = array(
                        'mensaje'       => 'Equipo se encuentra asignado a otra pauta, por favor verifique.',
                        'vacio'         => null);
                    return new JsonResponse($equipo_json);
                }
            } else {
                // Ya existe
                $equipo_array = array(
                        'mensaje'       => 'Ya agregó este equipo a la Pauta Actual',
                        'vacio'         => null);
                return new JsonResponse($equipo_array);
            }
        } else {
            $var = array('mensaje' => 'Equipo no encontrado','vacio' => null);
            return new JsonResponse($var);
        }
    }

    /*
     * Verifica la Salida del Equipo por el codigo de Barra
     */
    public function verificarSalidaEquipoAction(Request $request) {
        $codigo   = strtoupper($request->request->get('codigoBarra'));
        $pauta_id = $request->request->get('pauta');
        $em = $this->getDoctrine()->getManager();
        $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposInternos')->findOneByCodigoBarra($codigo);
        if(!empty($codigoequipo)) {
            $pauta       = $em->getRepository('ControlEquipoBundle:Pautas')->find($pauta_id); // Obtienes la Pauta
            $equipo      = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($codigoequipo->getId());

            $equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findOneBy(
                    array('pauta' => $pauta,'equipoInterno' => $equipo));

            if(!empty($equipos_pautas)) {
                if($equipos_pautas->getChequeoSalida() === FALSE) {
                    $equipos_pautas->setChequeoSalida(TRUE);
                    $em->persist($equipos_pautas);
                    $em->flush();
                    $equipo_json = array(
                            'chequeo'       => 'clsCheckTRUE',
                            'id'            => $equipos_pautas->getId(),
                            'vacio'         => 'no');
                    return new JsonResponse($equipo_json);
                } else{
                    $equipo_json = array(
                        'mensaje'       => 'Ya verifico la Salida de este equipo en la Pauta Actual.',
                        'vacio'         => null);
                    return new JsonResponse($equipo_json);
                }
            } else {
                // Ya existe
                $equipo_json = array(
                        'mensaje'       => 'Este Equipo no pertenece a esta Pauta, por favor verifique',
                        'vacio'         => null);
                return new JsonResponse($equipo_json);
            }
        } else {
            $var = array('mensaje' => 'Equipo no encontrado','vacio' => null);
            return new JsonResponse($var);
        }
    }

    /*
     * Verifica la Entrada del Equipo por el codigo de Barra
     */
    public function verificarEntradaEquipoAction(Request $request) {
        $codigo   = strtoupper($request->request->get('codigoBarra'));
        $pauta_id = $request->request->get('pauta');
        $em = $this->getDoctrine()->getManager();
        $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposInternos')->findOneByCodigoBarra($codigo);
        if(!empty($codigoequipo)) {
            $pauta       = $em->getRepository('ControlEquipoBundle:Pautas')->find($pauta_id); // Obtienes la Pauta
            $equipo      = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($codigoequipo->getId());

            $equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findOneBy(
                    array('pauta' => $pauta,'equipoInterno' => $equipo));

            if(!empty($equipos_pautas)) {
                // No existe, lo agregamos a la lista
                // Pero antes verificamos si el equipo esta disponible

                //$equipos_pautas->getChequeoSalida(); die;
                if($equipos_pautas->getChequeoEntrada() === FALSE) {
                    $equipos_pautas->setChequeoEntrada(TRUE);
                    $em->persist($equipos_pautas);
                    $em->flush();

                    // Actualizamos el Estado del Equipo a Activo
                    $equipo->setEstatus(1);
                    $em->persist($equipo);
                    $em->flush();
                    $equipo_json = array(
                            'chequeo'       => 'clsCheckTRUE',
                            'id'            => $equipos_pautas->getId(),
                            'vacio'         => 'no');
                    return new JsonResponse($equipo_json);
                } else{
                    $equipo_json = array(
                        'mensaje'       => 'Ya verifico la entrada de este equipo en la Pauta Actual.',
                        'vacio'         => null);
                    return new JsonResponse($equipo_json);
                }
            } else {
                // Ya existe
                $equipo_json = array(
                        'mensaje'       => 'Este Equipo no pertenece a esta Pauta, por favor verifique',
                        'vacio'         => null);
                return new JsonResponse($equipo_json);
            }
        } else {
            $var = array('mensaje' => 'Equipo no encontrado','vacio' => null);
            return new JsonResponse($var);
        }
    }

    /*
     * Verifica Si no se ha chequeado la entrada de Equipos
     */
    public function verificarPautaSalidaAction(Request $request) {
        $pauta_id = $request->request->get('pauta');
        $em = $this->getDoctrine()->getManager();
        $pauta       = $em->getRepository('ControlEquipoBundle:Pautas')->find($pauta_id); // Obtienes la Pauta

        $equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findBy(
                    array('pauta' => $pauta));

            if(!empty($equipos_pautas)) {
                $n = 0;
                foreach ($equipos_pautas as $equipo_pauta) {
                    if($equipo_pauta->getChequeoSalida() === FALSE) {
                        $n++;
                    }
                }

                if($n == 0) {
                    $equipo_json = array(
                            'vacio'         => 'si');
                    return new JsonResponse($equipo_json);
                } else {
                    $equipo_json = array(
                            'cantidad'      => $n,
                            'vacio'         => 'no');
                    return new JsonResponse($equipo_json);
                }
            } else {
                $equipo_json = array(
                            'mensaje'       => 'No hay equipos que verificar en esta pauta, por favor registre Equipos en la Pauta',
                            'vacio'         => null);
                return new JsonResponse($equipo_json);
            }
    }

    /*
     * Verifica Si no se ha chequeado la entrada de Equipos
     */
    public function verificarPautaEntradaAction(Request $request) {
        $pauta_id = $request->request->get('pauta');
        $em = $this->getDoctrine()->getManager();
        $pauta       = $em->getRepository('ControlEquipoBundle:Pautas')->find($pauta_id); // Obtienes la Pauta

        $equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findBy(
                    array('pauta' => $pauta));

            if(!empty($equipos_pautas)) {
                $n = 0;
                foreach ($equipos_pautas as $equipo_pauta) {
                    if($equipo_pauta->getChequeoEntrada() === FALSE) {
                        $n++;
                    }
                }

                if($n == 0) {
                    $equipo_json = array(
                            'vacio'         => 'si');
                    return new JsonResponse($equipo_json);
                } else {
                    $equipo_json = array(
                            'cantidad'      => $n,
                            'vacio'         => 'no');
                    return new JsonResponse($equipo_json);
                }
            } else {
                $equipo_json = array(
                            'mensaje'       => 'No hay equipos que verificar en esta pauta, por favor registre Equipos en la Pauta.',
                            'vacio'         => null);
                return new JsonResponse($equipo_json);
            }
    }

    /*
     * Eliminar equipo de una pauta creada
     */
    public function eliminarEquipoPautaAction(Request $request) {
        $pauta_id = $request->request->get('equipo_pauta');
        $codigo = $request->request->get('equipo');
        $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:EquiposPautas')->find($pauta_id);

            if (!$entity) {
                //throw $this->createNotFoundException('Unable to find EquiposPautas entity.');
                $var = array('mensaje' => 'Error al Eliminar Equipo de la Pauta.');
                return new JsonResponse($var);
            }
            // Eliminamos el equipo
            $em->remove($entity);
            $em->flush();

            // Luego cambiamos el estatus del equipo a disponible
            $equiposinternos = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($codigo);
                if (!$equiposinternos) {
                    //throw $this->createNotFoundException('Unable to find EquiposPautas entity.');
                    $var = array('mensaje' => 'Error al cambiar estatus del equipo.');
                    return new JsonResponse($var);
                }
                $equiposinternos->setEstatus(1);
                $em->persist($equiposinternos);
                $em->flush();
            $var = array('resultado' => 'ok');
            return new JsonResponse($var);
    }

    /*
     * Obtiene el Equipo por el codigo de Barra
     */
    public function insertarModeloAction(Request $request) {
        $modelo   = \strtoupper($request->request->get('modelo'));
        $marca_id = $request->request->get('marca');
        $em = $this->getDoctrine()->getManager();
        $modelomarca = $em->getRepository('ControlEquipoBundle:Modelos')->findOneByDescripcionModelo($modelo);
        if(empty($modelomarca)) {
            $marca  = $em->getRepository('ControlEquipoBundle:Marcas')->find($marca_id); // Obtienes la Marca
                    $modelo_obj = new Modelos();
                    // Seteamos los valores de la Operacion
                    $modelo_obj->setDescripcionModelo(\strtoupper($modelo));
                    $modelo_obj->setMarca($marca);
                    $em->persist($modelo_obj);
                    $em->flush();

                    $modelo_array = array(
                            'id'            => $modelo_obj->getId(),
                            'modelo'        => $modelo_obj->getDescripcionModelo(),
                            'vacio'         => 'no');

                    return new JsonResponse($modelo_array);
        } else {
            $var = array('mensaje' => 'Ya existe el Modelo que ingresó','vacio' => null);
            return new JsonResponse($var);
        }
    }

    /*
     * Eliminar equipo de una pauta creada
     */
    public function eliminarModeloAction(Request $request) {
        $modelo = $request->request->get('modelo');
        //$codigo = $request->request->get('equipo');
        $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:Modelos')->find($modelo);

            if (!$entity) {
                //throw $this->createNotFoundException('Unable to find EquiposPautas entity.');
                $var = array('mensaje' => 'Error al Eliminar Modelo.');
                return new JsonResponse($var);
            }
            // Eliminamos el equipo
            $em->remove($entity);
            $em->flush();
            $var = array('resultado' => 'ok');
            return new JsonResponse($var);
    }

    // Para los Equipos Externos
    /*
     * Obtiene el Equipo por el codigo de Barra
     */
    public function busquedaEquipoExternoAction(Request $request) {
        $codigo         = $request->request->get('codigoBarra');
        $propietario    = $request->request->get('propietario');
        $tipo           = $request->request->get('tipo');

        $em = $this->getDoctrine()->getManager();
        $codigoequipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findOneByCodigoBarra($codigo);
        if(!empty($codigoequipo)) {
            $entity = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($codigoequipo->getId());

            /*$equipos_pautas = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findBy(
                    array('pauta' => $pauta,'equipoInterno' => $equipo));*/
                // No existe, lo agregamos a la lista
                // Pero antes verificamos si el equipo esta disponible
                $propietario        = $entity->getPropietarioId();
                $tipo_propietario   = $entity->getTipoPropietario();
                if($tipo_propietario == 1) {
                    // Consulto en la tabla usuario
                    $usuarios = $em->getRepository('UsuarioBundle:Perfil')->find($propietario);
                    $propietario = \strtoupper($usuarios->getPrimerNombre()) . ' ' . strtoupper($usuarios->getPrimerApellido());
                } elseif($tipo_propietario == 2) {
                    // Consulto en la tabla visita
                    $usuarios = $em->getRepository('FrontendVisitasBundle:Usuario')->find($propietario);
                    $nombres = explode(" ", $usuarios->getNombres());
                    $apellidos = explode(" ", $usuarios->getApellidos());
                    $propietario = strtoupper($nombres[0]) . ' ' . strtoupper($apellidos[0]);
                }
                return $this->render('ControlEquipoBundle:EquiposExternos:showresult.html.twig', array(
                    'entity'      => $entity,
                    'propietario' => $propietario
                ));

        } else {
                if($tipo == 1) {
                    $usuarios = $em->getRepository('UsuarioBundle:Perfil')->find($propietario);
                    if(!empty($usuarios)) {
                        $usuario = array(
                        'nombres'           => $usuarios->getPrimerNombre().' '.$usuarios->getPrimerApellido(),
                        'cedula'            => $usuarios->getCedula(),
                        'tipo_propietario'  => 1,
                        'propietario'       => $usuarios->getUser()->getId());
                    }
                } elseif($tipo == 2) {
                    $em = $this->getDoctrine()->getManager();
                    $usuarios = $em->getRepository('ControlEquipoBundle:Usuario')->find($propietario);

                    if(!empty($usuarios)) {
                        $nombres = explode(" ", $usuarios->getNombres());
                        $apellidos = explode(" ", $usuarios->getApellidos());
                        $usuario = array(
                            'nombres' => \strtoupper($nombres[0]) .' '.\strtoupper($apellidos[0]),
                            'cedula' => $usuarios->getCedula(),
                            'tipo_propietario' => 2,
                            'propietario' => $usuarios->getId());
                    }
                }
            //$entity = new \Frontend\ControlEquipoBundle\Entity\EquiposExternos();
            //$form_equipo = new \Frontend\ControlEquipoBundle\Form\EquiposExternosType();
            //$form   = $form_equipo->createCreateForm($entity);
            $view = '<div class="alert alert-danger" role="alert">Equipo no encontrado!</div>';
            $view .= $this->renderView('ControlEquipoBundle:EquiposExternos:new_equipo.html.twig', array(
                    //'entity' => $entity,
                    'usuario'=> $usuario,
                    'tipo'      => $tipo,
                    //'form'  => $form->createView(),
                    ));
            //$url = $this->generateUrl('equipoexterno_new',array('id' => $propietario,'tipo' => $tipo));
            //return new Response('<div class="alert alert-danger" role="alert">Equipo no encontrado! Realice la búsqueda nuevamente.</div>'
            //        . '<a href="'.$url.'" class="btn btn-info">Registrar Equipo</a>');
            return new Response($view);
        }
    }

}
