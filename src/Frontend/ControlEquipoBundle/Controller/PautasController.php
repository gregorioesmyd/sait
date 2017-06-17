<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\Pautas;
use Frontend\ControlEquipoBundle\Entity\Operaciones;
#use Frontend\ControlEquipoBundle\Entity\TiposOperaciones;
#use Frontend\ControlEquipoBundle\Entity\EquiposPautas;
use Frontend\ControlEquipoBundle\Form\PautasType;

/**
 * Pautas controller.
 *
 */
class PautasController extends Controller
{

    /**
     * Lists all Pautas entities from responsable user.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Obtengo las pautas del usuario
        $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
        $responsable  = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $query3 = $em->createQuery('SELECT o
                                    FROM ControlEquipoBundle:Operaciones o
                                    JOIN o.pauta p
                                    WHERE o.tipoOperacion = 1
                                    AND o.reponsableUsuario = :responsable
                                    ORDER BY p.estatus ASC'
                                )->setParameter('responsable',$responsable);
        $pautasmias  = $query3->getResult();

        if(!empty($pautasmias)) {
            foreach ($pautasmias as $entity)
            {
                $unidad[$entity->getPauta()->getId()] = $entity->getPauta()->getResponsable()->getNivelorganizacional();
                 // Obtenemos la Cantidad de Equipos
                $query2 = $em->createQuery(
                                    'SELECT COUNT(o)
                                    FROM ControlEquipoBundle:EquiposPautas o
                                    WHERE o.pauta = :pauta_id'
                                )->setParameter('pauta_id', $entity->getPauta()->getId());
                $numero  = $query2->getSingleResult();
                $nequipo[$entity->getPauta()->getId()] = $numero[1];
            }

            return $this->render('ControlEquipoBundle:Pautas:index.html.twig', array(
                                'entities'          => $pautasmias,
                                'unidadResponsable' => $unidad,
                                'nequipo'           => $nequipo
            ));
        } else {
            return $this->render('ControlEquipoBundle:Pautas:index.html.twig', array(
                                'entities'          => $pautasmias  ));
        }
    }


    /**
     * Lists all Pautas entities.
     *
     */
    public function todasLasPautasAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Seleccionamos las Pautas que estan confirmadas y que aun no han finalizado
        $query = $em->createQuery(
                'SELECT p FROM ControlEquipoBundle:Pautas p WHERE p.estatus > 1 and p.estatus < 4 ORDER BY p.estatus DESC');
        $entities = $query->getResult();


        if(!empty($entities)) {
            foreach ($entities as $entity)
            {
                $unidad[$entity->getId()] = $entity->getResponsable()->getNivelorganizacional();
                 // Obtenemos la Cantidad de Equipos
                $query2 = $em->createQuery(
                                    'SELECT COUNT(o)
                                    FROM ControlEquipoBundle:EquiposPautas o
                                    WHERE o.pauta = :pauta_id'
                                )->setParameter('pauta_id', $entity->getId());
                $numero  = $query2->getSingleResult();
                $nequipo[$entity->getId()] = $numero[1];
            }

            return $this->render('ControlEquipoBundle:Pautas:todas.html.twig', array(
                                'entities'          => $entities,
                                'unidadResponsable' => $unidad,
                                'nequipo'           => $nequipo
            ));
        } else {
            return $this->render('ControlEquipoBundle:Pautas:todas.html.twig', array(
                                'entities'          => $entities));
        }
    }

    /**
     * Lists all Pautas Programadas entities.
     *
     */
    public function pautaprogramadaAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:Pautas')->findBy(array('tipoPauta' => '1'));

        if(!empty($entities)) {
            foreach ($entities as $entity)
            {
                $unidad[$entity->getId()] = $entity->getResponsable()->getNivelorganizacional();
                $query = $em->createQuery(
                                    'SELECT o
                                    FROM ControlEquipoBundle:Operaciones o
                                    WHERE o.pauta = :pauta_id
                                    ORDER BY o.tipoOperacion DESC'
                                )->setParameter('pauta_id', $entity->getId());
                $query->setFirstResult(0);
                $query->setMaxResults(1);
                $operaciones = $query->getOneOrNullResult();
                $estatus[$entity->getId()] = $operaciones->getTipoOperacion();
                $query2 = $em->createQuery(
                                    'SELECT COUNT(o)
                                    FROM ControlEquipoBundle:EquiposPautas o
                                    WHERE o.pauta = :pauta_id'
                                )->setParameter('pauta_id', $entity->getId());
                $numero  = $query2->getSingleResult();
                $nequipo[$entity->getId()] = $numero[1];
            }
            return $this->render('ControlEquipoBundle:Pautas:pautaspro.html.twig', array(
                                'entities'          => $entities,
                                'unidadResponsable' => $unidad,
                                'estatus'           => $estatus,
                                'tipo'              => 1,
                                'nequipo'           => $nequipo
            ));
        } else {
            return $this->render('ControlEquipoBundle:Pautas:pautaspro.html.twig', array(
                                'entities'          => $entities,
                                'tipo'              => 1
                ));
        }
    }


    /**
     * Lists all Pautas Programadas entities.
     *
     */
    public function pautanoprogramadaAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:Pautas')->findBy(array('tipoPauta' => '2'));

        if(!empty($entities)) {
            foreach ($entities as $entity)
            {
                $unidad[$entity->getId()] = $entity->getResponsable()->getNivelorganizacional();
                $query = $em->createQuery(
                                    'SELECT o
                                    FROM ControlEquipoBundle:Operaciones o
                                    WHERE o.pauta = :pauta_id
                                    ORDER BY o.tipoOperacion DESC'
                                )->setParameter('pauta_id', $entity->getId());
                $query->setFirstResult(0);
                $query->setMaxResults(1);
                $operaciones = $query->getOneOrNullResult();
                $estatus[$entity->getId()] = $operaciones->getTipoOperacion();

                $query2 = $em->createQuery(
                                    'SELECT COUNT(o)
                                    FROM ControlEquipoBundle:EquiposPautas o
                                    WHERE o.pauta = :pauta_id'
                                )->setParameter('pauta_id', $entity->getId());
                $numero  = $query2->getSingleResult();
                $nequipo[$entity->getId()] = $numero[1];

            }
            return $this->render('ControlEquipoBundle:Pautas:pautanopro.html.twig', array(
                                'entities'          => $entities,
                                'unidadResponsable' => $unidad,
                                'estatus'           => $estatus,
                                'tipo'              => 2,
                                'nequipo'           => $nequipo
            ));
        } else {
            return $this->render('ControlEquipoBundle:Pautas:pautanopro.html.twig', array(
                                'entities'          => $entities,
                                'tipo'              => 2));
        }
    }
    /**
     * Creates a new Pautas entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Pautas();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $datos=$request->request->all();
        $pauta=$datos['controlequipobundle_pautas']['tipoPauta'];
        if($pauta == 1) {
            $pauta = 'Pauta Programada';
        } elseif($pauta == 2) {
            $pauta = 'Pauta No Programada';
        }

        if ($form->isValid()) {
            $fechaDesde = strtotime(date_format($entity->getFechaDesde(), 'd-m-Y'));
            $fechaHasta= strtotime(date_format($entity->getFechaHasta(), 'd-m-Y'));

            if($fechaDesde > $fechaHasta){
                  $this->get('session')->getFlashBag()->add('alert', 'La fecha hasta debe ser mayor o igual a la fecha desde.');
                  return $this->render('ControlEquipoBundle:Pautas:new.html.twig', array(
                      'entity' => $entity,
                      'form'   => $form->createView(),
                      'pauta'  => $pauta
                  ));
            }

            $em = $this->getDoctrine()->getManager();
            $entity->setEstatus(1);
            $em->persist($entity);
            $em->flush();

            // Creamos la Operacion que se genero // En este caso Pauta Creada
            $datos=$request->request->all();
            $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
            $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            $operacion      = new Operaciones(); // Instanciamos el Objeto Operaciones
                $pauta = $em->getRepository('ControlEquipoBundle:Pautas')->find($entity->getId());
                // Fecha / Hora Actual
                $fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
                // Seteamos los valores de la Operacion
                $operacion->setFechaOperacion($fecha_operacion);
                $operacion->setReponsableUsuario($responsableUsuario);
                $operacion->setTipoOperacion(1);
                $operacion->setPauta($pauta);
                $em->persist($operacion);
                $em->flush();
            return $this->redirect($this->generateUrl('pauta_show', array('id' => $entity->getId())));
        }


        return $this->render('ControlEquipoBundle:Pautas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'pauta'  => $pauta
        ));
    }

    /**
     * Creates a form to create a Pautas entity.
     *
     * @param Pautas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pautas $entity)
    {
        $form = $this->createForm(new PautasType(), $entity, array(
            'action' => $this->generateUrl('pauta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Pautas entity.
     *
     */
    public function newProgramadaAction()
    {
        $entity = new Pautas();
        $entity->setTipoPauta('1');
        $entity->setAprobado(TRUE);
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:Pautas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'pauta'  => 'Pauta Programada'
        ));
    }

    /**
     * Displays a form to create a new Pautas entity.
     *
     */
    public function newNprogramadaAction()
    {
        $entity = new Pautas();
        $entity->setTipoPauta('2');
        $entity->setFechaDesde(new \DateTime(\date('Y-m-d')));
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:Pautas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'pauta'  => 'Pauta No Programada'
        ));
    }

    /**
     * Finds and displays a Pautas entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $unidad = $entity->getResponsable()->getNivelorganizacional();
        $pauta_id = $entity->getId();
        $equipos  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($pauta_id);
        if(!empty($equipos)) {
            foreach ($equipos as $equipo) {
                $fotoreferencia[$equipo->getEquipoInterno()->getId()] = $equipo->getEquipoInterno()->getFotoReferencia();
            }
        } else {
            $fotoreferencia = null;
        }
            // ahora obtenemos el estatus de la Pauta
            $estatus = $entity->getEstatus();
                if($estatus == 5) {
                    $this->get('session')->getFlashBag()->add('alert', 'Pauta finalizada, no se puede verificar.');
                    return $this->redirect($this->generateUrl('pautas'));
                } elseif($estatus == 4) {
                    $this->get('session')->getFlashBag()->add('alert', 'Se confirmó la entrada, no se puede verificar.');
                    return $this->redirect($this->generateUrl('pautas'));
                } elseif($estatus == 3) {
                    $this->get('session')->getFlashBag()->add('alert', 'Se confirmó la salida, no se puede verificar.');
                    return $this->redirect($this->generateUrl('pautas'));
                } elseif($estatus == 2) {
                    $this->get('session')->getFlashBag()->add('alert', 'Pauta confirmada, no se puede verificar.');
                    return $this->redirect($this->generateUrl('pautas'));
                } elseif($estatus == 1) {
                    $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
                    $query = $em->createQuery(
                                'SELECT o
                                FROM ControlEquipoBundle:Operaciones o
                                WHERE o.pauta = :pauta_id
                                AND o.tipoOperacion = 1
                                ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $pauta_id);
                    $operaciones = $query->getOneOrNullResult();
                    if($operaciones->getReponsableUsuario()->getUser()->getId() != $idusuario) {
                        $this->get('session')->getFlashBag()->add('alert', 'Solo la persona que creó la Pauta puede Confirmarla.');
                        return $this->redirect($this->generateUrl('pautas'));
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('alert', 'Error al ingresar a la Pauta Seleccionada.');
                    return $this->redirect($this->generateUrl('pautas'));
                }

        return $this->render('ControlEquipoBundle:Pautas:show.html.twig', array(
            'entity'            => $entity,
            'equipos'           => $equipos,
            'unidadDependencia' => $unidad,
            'fotoReferencia'    => $fotoreferencia,
            'operaciones'       => $operaciones,
            'estatus'           => $estatus
        ));
    }

    /**
     * Finds and displays a Pautas entity.
     *
     */
    public function verAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $unidad = $entity->getResponsable()->getNivelorganizacional();
        $deleteForm = $this->createDeleteForm($id);

        $equipos     = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($id);

        if(!empty($equipos)) {
            foreach ($equipos as $equipo) {
                $fotoreferencia[$equipo->getEquipoInterno()->getId()] = $equipo->getEquipoInterno()->getFotoReferencia();
            }
        } else {
            $fotoreferencia = null;
        }

        return $this->render('ControlEquipoBundle:Pautas:ver.html.twig', array(
            'entity'            => $entity,
            'delete_form'       => $deleteForm->createView(),
            'unidadDependencia' => $unidad,
            'equipos'           => $equipos,
            'fotoReferencia'    => $fotoreferencia
        ));
    }

    /**
     * Finds and displays a Pautas entity.
     *
     */
    public function seguimientoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $unidad = $entity->getResponsable()->getNivelorganizacional();

        $equipos  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($id);
        if(!empty($equipos)) {
            foreach ($equipos as $equipo) {
                $fotoreferencia[$equipo->getEquipoInterno()->getId()] = $equipo->getEquipoInterno()->getFotoReferencia();
            }
        } else {
            $fotoreferencia = null;
        }
            // Verificamos el Estatus de la Pauta
            $query = $em->createQuery(
                                'SELECT o
                                   FROM ControlEquipoBundle:Operaciones o
                                  WHERE o.pauta = :pauta_id
                               ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $id);
            $query->setFirstResult(0);
            $query->setMaxResults(1);
            $id_estatus = $query->getOneOrNullResult();
            $estatus = $id_estatus->getTipoOperacion();

            // Seguimiento
            $operaciones = $em->getRepository('ControlEquipoBundle:Operaciones')->findBy(array('pauta' => $id),array('tipoOperacion' => 'ASC'));

        return $this->render('ControlEquipoBundle:Pautas:seguimiento.html.twig', array(
            'entity'      => $entity,
            'unidadDependencia' => $unidad,
            'fotoReferencia'    => $fotoreferencia,
            'estatus'           => $estatus,
            'operaciones'       => $operaciones,
            'equipos'           => $equipos
        ));
    }

    /**
     * Verifica la Pauta y Los Equipos.
     *
     */
    public function verificarSalidaAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $unidad = $entity->getResponsable()->getNivelorganizacional();
        $equipos  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($id);
        if(!empty($equipos)) {
            foreach ($equipos as $equipo) {
                $fotoreferencia[$equipo->getEquipoInterno()->getId()] = $equipo->getEquipoInterno()->getFotoReferencia();
            }
        } else {
            $fotoreferencia = null;
        }
            // ahora obtenemos el seguimiento de la Pauta
            $query = $em->createQuery(
                                'SELECT o
                                FROM ControlEquipoBundle:Operaciones o
                                WHERE o.pauta = :pauta_id
                                ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $id)
                             ->setFirstResult(0)
                             ->setMaxResults(1);
            $operaciones = $query->getOneOrNullResult();
            $estatus = $operaciones->getTipoOperacion();
            if($estatus != 2) {
                $this->get('session')->getFlashBag()->add('alert', 'Error al Verificar la Salida, Pauta confirmada o finalizada.');
                return $this->redirect($this->generateUrl('pautas'));
            }
        return $this->render('ControlEquipoBundle:Pautas:verificarsalida.html.twig', array(
            'entity'            => $entity,
            'equipos'           => $equipos,
            'unidadDependencia' => $unidad,
            'fotoReferencia'    => $fotoreferencia,
            'operaciones'       => $operaciones,
            'estatus'           => $estatus
        ));
    }

    /**
     * Verifica la Pauta y Los Equipos.
     *
     */
    public function verificarEntradaAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $unidad = $entity->getResponsable()->getNivelorganizacional();
        $equipos  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($id);
        if(!empty($equipos)) {
            foreach ($equipos as $equipo) {
                $fotoreferencia[$equipo->getEquipoInterno()->getId()] = $equipo->getEquipoInterno()->getFotoReferencia();
            }
        } else {
            $fotoreferencia = null;
        }
            // ahora obtenemos el seguimiento de la Pauta
            $query = $em->createQuery(
                                'SELECT o
                                FROM ControlEquipoBundle:Operaciones o
                                WHERE o.pauta = :pauta_id
                                ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $id)
                             ->setFirstResult(0)
                             ->setMaxResults(1);
            $operaciones = $query->getOneOrNullResult();
            $estatus = $operaciones->getTipoOperacion();
            if($estatus != 3) {
                $this->get('session')->getFlashBag()->add('alert', 'Error al Verificar la Entrada, Pauta confirmada o finalizada.');
                return $this->redirect($this->generateUrl('pautas'));
            }
        return $this->render('ControlEquipoBundle:Pautas:verificarentrada.html.twig', array(
            'entity'            => $entity,
            'equipos'           => $equipos,
            'unidadDependencia' => $unidad,
            'fotoReferencia'    => $fotoreferencia,
            'operaciones'       => $operaciones,
            'estatus'           => $estatus
        ));
    }

    /**
     * Displays a form to edit an existing Pautas entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        $query = $em->createQuery(
                                'SELECT o
                                FROM ControlEquipoBundle:Operaciones o
                                WHERE o.pauta = :pauta_id
                                AND o.tipoOperacion = 1
                                ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $id);
        $operaciones = $query->getOneOrNullResult();

        $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();

        if($operaciones->getReponsableUsuario()->getUser()->getId() != $idusuario) {
            $this->get('session')->getFlashBag()->add('alert', 'No puede modificar la Pauta, sólo puede modificarla la persona que la creó.');
            return $this->redirect($this->generateUrl('pautas'));
        }

        if($entity->getEstatus() > 1) {
            $this->get('session')->getFlashBag()->add('alert', 'No puede modificar la Pauta, ya se ha confirmado.');
            return $this->redirect($this->generateUrl('pautas'));
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('ControlEquipoBundle:Pautas:edit.html.twig', array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
        ));
    }

    /**
     * Confirmar pauta Pautas entity.
     *
     */
    public function confirmarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_pauta = $request->request->get('id_pauta');
        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id_pauta);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }
        // Verificamos si esta Pauta posee equipos Internos
        $equipospautas  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($entity->getId());
        if(empty($equipospautas)) {
            $this->get('session')->getFlashBag()->add('alert', 'No puede confirmar la Pauta, no hay que equipos que verificar. Por favor añada equipos a la Pauta antes de confirmar.');
            return $this->redirect($this->generateUrl('pauta_show', array('id' => $entity->getId())));
        }

        $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
                    $query = $em->createQuery(
                                'SELECT o
                                FROM ControlEquipoBundle:Operaciones o
                                WHERE o.pauta = :pauta_id
                                AND o.tipoOperacion = 1
                                ORDER BY o.tipoOperacion DESC'
                            )->setParameter('pauta_id', $id_pauta);
                    $operaciones = $query->getOneOrNullResult();
                    if($operaciones->getReponsableUsuario()->getUser()->getId() != $idusuario) {
                        $this->get('session')->getFlashBag()->add('alert', 'Solo la persona que creó la Pauta puede Confirmarla.');
                        return $this->redirect($this->generateUrl('pautas'));
                    }

        // Confirmamos la Pauta en la Tabla Operaciones

        $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $operacion      = new Operaciones(); // Instanciamos el Objeto Operaciones
        $pauta          = $em->getRepository('ControlEquipoBundle:Pautas')->find($entity->getId());
        // Fecha / Hora Actual
        $fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
        // Seteamos los valores de la Operacion
        $operacion->setFechaOperacion($fecha_operacion);
        $operacion->setReponsableUsuario($responsableUsuario);
        $operacion->setTipoOperacion(2);
        $operacion->setPauta($pauta);
        $em->persist($operacion);
        $em->flush();
        if($entity->getTipoPauta() == 2) {
            // Es una Pauta No programada, por tanto vamos a confirmar la salida de una vez
            $operacion_salida      = new Operaciones();
            $operacion_salida->setFechaOperacion($fecha_operacion);
            $operacion_salida->setReponsableUsuario($responsableUsuario);
            $operacion_salida->setTipoOperacion(3);
            $operacion_salida->setPauta($pauta);
            $em->persist($operacion_salida);
            $em->flush();

            $entity->setEstatus(3);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Se ha verificado la Pauta y confirmado la Salida correctamente.');
            return $this->redirect($this->generateUrl('pautas'));
        }
        $entity->setEstatus(2);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Se ha confirmado la Pauta correctamente.');
        return $this->redirect($this->generateUrl('pautas'));
    }


    /**
     * Confirmar salida de equipos en una Pauta.
     *
     */
    public function confirmarSalidaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$datos = $request->request->all();
        $id_pauta = $request->request->get('id_pauta');
        //$pauta = $datos['id_pauta'];
        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id_pauta);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }

        // Consultamos Los Equipos Pautas
        $equipospautas  = $em->getRepository('ControlEquipoBundle:EquiposPautas')->findByPauta($entity->getId());

        if(empty($equipospautas)) {
            $this->get('session')->getFlashBag()->add('alert', 'No hay equipos que verificar. Por favor, añada equipos a la Pauta Actual.');
            return $this->redirect($this->generateUrl('pauta_versal',array('id' => $id_pauta)));
        }

        // Confirmamos la salida de la Pauta en la Tabla Operaciones con el Usuario que la realiza
        $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
        $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $operacion      = new Operaciones(); // Instanciamos el Objeto Operaciones
        $pauta          = $em->getRepository('ControlEquipoBundle:Pautas')->find($entity->getId());
        // Fecha / Hora Actual
        $fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
        // Seteamos los valores de la Operacion
        $operacion->setFechaOperacion($fecha_operacion);
        $operacion->setReponsableUsuario($responsableUsuario);
        $operacion->setTipoOperacion(3);
        $operacion->setPauta($pauta);
        $em->persist($operacion);
        $em->flush();
        // Cambiamos el estatus de la Pauta
        $entity->setEstatus(3);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Se ha confirmado la Salida de Equipos satisfactoriamente.');
        return $this->redirect($this->generateUrl('pautas'));
    }

    /**
     * Confirmar salida de equipos en una Pauta.
     *
     */
    public function confirmarEntradaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_pauta = $request->request->get('id_pauta');
        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id_pauta);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }

        // Confirmamos la Pauta en la Tabla Operaciones
        $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
        $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $operacion      = new Operaciones(); // Instanciamos el Objeto Operaciones
        $pauta          = $em->getRepository('ControlEquipoBundle:Pautas')->find($entity->getId());
        // Fecha / Hora Actual
        $fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
        // Seteamos los valores de la Operacion
        $operacion->setFechaOperacion($fecha_operacion);
        $operacion->setReponsableUsuario($responsableUsuario);
        $operacion->setTipoOperacion(4);
        $operacion->setPauta($pauta);
        $em->persist($operacion);
        $em->flush();

        // Actualizamos el campo de Referencia Estatus en la Entidad Pauta
        $entity->setEstatus(4);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Se ha confirmado la Entrada de Equipos en la Pauta.');
        return $this->redirect($this->generateUrl('pautas'));
    }

    /**
    * Creates a form to edit a Pautas entity.
    *
    * @param Pautas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pautas $entity)
    {
        $form = $this->createForm(new PautasType(), $entity, array(
            'action' => $this->generateUrl('pauta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Pautas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pautas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Se ha editado la Pauta correctamente.');
            return $this->redirect($this->generateUrl('pauta_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:Pautas:edit.html.twig', array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }
    /**
     * Deletes a Pautas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:Pautas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pautas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pautas'));
    }

    /**
     * Creates a form to delete a Pautas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pauta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar Pauta'))
            ->getForm()
        ;
    }
}
