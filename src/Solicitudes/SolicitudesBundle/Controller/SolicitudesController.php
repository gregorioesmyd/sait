<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Solicitudes\SolicitudesBundle\Entity\Solicitudes;
use Solicitudes\SolicitudesBundle\Form\SolicitudesType;



/**
 * Solicitudes controller.
 *
 */
class SolicitudesController extends Controller
{
    /**
     * Lista todas las solicitudes creadas
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /* Se buscan todas las solicitudes */
        $entities = $em->getRepository('SolicitudesBundle:Solicitudes')->findAll();

        /* Retorno a la vista */
        return $this->render('SolicitudesBundle:Solicitudes:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lista solo las solicitudes del usuario logeado
     *
     */
    public function miindexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //VERIFICA SI EL USUARIO ESTA LOGUEADO
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            throw new AccessDeniedException();
        }

        /* si el usuario esta logeado obtengo su id */
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();

        /* se buscan las solicitudes que creó el usuario conectado */
        $entities = $em->getRepository('SolicitudesBundle:Solicitudes')->findBySolicitante($idusuario);
   
        /* retorna a la vista */
        return $this->render('SolicitudesBundle:Solicitudes:index.html.twig', array(
            'entities' => $entities,
        ));

    }

################################################################################################################################
#                                                  CREATE
################################################################################################################################

    /**
     * Crear la Solicitud
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Solicitudes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
           
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);


            /* Funcion que realiza las validaciones para los campos colletions (responsables, unidades, requerimientos) */
            $alert = $this->validaciones($entity);           
            
            /**
            * VALIDA QUE NO EXISTAN ERRORES
            *   (De no existir Se crea el registro de la solicitud)
            *   (En caso de que existan errores envia con las alertas a la vista NEW)
            */
            if($alert == 0){

                //VERIFICAR SI EL USUARIO ESTA LOGUEADO
                if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
                {
                    throw new AccessDeniedException();
                }

                /* obtengo informacion del usuario logeado */
                $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
                $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
                
                $estatus = $em->getRepository('SolicitudesBundle:Estatus')->findOneById(1);

                $entity->setSolicitante($usuario);
                $entity->setEstatus($estatus);
                $em->flush();

                return $this->redirect($this->generateUrl('solicitudesapp_show', array('id' => $entity->getId())));
            }         
        }
        return $this->render('SolicitudesBundle:Solicitudes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Solicitudes entity.
     *
     * @param Solicitudes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Solicitudes $entity)
    {
        $form = $this->createForm(new SolicitudesType(), $entity, array(
            'action' => $this->generateUrl('solicitudesapp_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Crea el formulario que se mostrará en la vista de Nuevas Solicitudes
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();       

        /* Instancia la clase Solicitudes */
        $entity = new Solicitudes();

        /* Crea el formulario de la clase instanciada, llamando a la funcion createCreateForm() */
        $form   = $this->createCreateForm($entity);

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            throw new AccessDeniedException();
        }    
        
        /* Retorno a la vista */
        return $this->render('SolicitudesBundle:Solicitudes:new.html.twig', array(
        'entity' => $entity,
        'form'   => $form->createView(),
        ));     
    }


    /**
     * Busca los detalles de una solicitud.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* Busca los datos de una solicitud de acuerdo al id obtenido */
        $entity = $em->getRepository('SolicitudesBundle:Solicitudes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe esta Solicitud');
        }

        /* Retorno a la vista */
        return $this->render('SolicitudesBundle:Solicitudes:show.html.twig', array(
            'entity'      => $entity,
        ));
    }


    /**
    *   Actualiza el estatus de la solicitud
    *
    */
    public function actualiza_estatusAction($id, $estatus)
    {

        $em = $this->getDoctrine()->getManager();

        /* Se busca la Solicitud de acuerdo al id enviado ($id) */
        $entity = $em->getRepository('SolicitudesBundle:Solicitudes')->find($id);

        /* Se busca el estatus segun el id del estatus enviado($estatus) */
        $estatus_id = $em->getRepository('SolicitudesBundle:Estatus')->find($estatus);
        
        /* Setea el estatus */
        $entity->setEstatus($estatus_id);        
       
        /* Inserta en BD*/
        $em->persist($entity);
        $em->flush();

        if($estatus == 2)
        {
            /* Redirecciona a la funcion showAction(), muestra el detalle de la solicitud */
            return $this->redirect($this->generateUrl('solicitudesapp_show', array('id' => $id)));
        }
        /* Redirecciona a la funcion indexAction(), para el listado global de solicitudes */
        return $this->redirect($this->generateUrl('solicitudesapp'));
    }



    /**
     * Crea el formulario que se mostrará en la vista de Editar solicitudes
     *
     */    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* Se busca la Solicitud de acuerdo al id enviado ($id) */
        $entity = $em->getRepository('SolicitudesBundle:Solicitudes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Solicitudes entity.');
        }

        /* Se crea el formulario de editar, llamando a la funcion createEditForm() */
        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        /* Se retorna a la vista */
        return $this->render('SolicitudesBundle:Solicitudes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           // 'delete_form' => $deleteForm->createView(),
        ));
    }

    

    /**
    * Creates a form to edit a Solicitudes entity.
    *
    * @param Solicitudes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */   
    private function createEditForm(Solicitudes $entity)
    {
        $form = $this->createForm(new SolicitudesType(), $entity, array(
            'action' => $this->generateUrl('solicitudesapp_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }


################################################################################################################################
#                                                  UPDATE
################################################################################################################################  
    /**
     * Edita la solicitud
     *
     */    
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        /* Se busca la informacion de la solicitud de acuerdo a un id */
        $entity = $em->getRepository('SolicitudesBundle:Solicitudes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Solicitudes entity.');
        }
        
        /* Se obtiene el estatus de la Solicitud */
        $status = $entity->getEstatus()->getId();
        $estatus = $em->getRepository('SolicitudesBundle:Estatus')->findOneById($status);


         /* Se obtiene el usuario que creo la Solicitud */
        $solicitante = $entity->getSolicitante()->getId();
        $usuario = $em->getRepository('UsuarioBundle:Perfil')->findOneById($solicitante);


        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) 
        {   
            /* Funcion que realiza las validaciones para los campos colletions (responsables, unidades, requerimientos) */
            $alert = $this->validaciones($entity);       

            /**
            * VALIDA QUE NO EXISTAN ERRORES
            *   (De no existir Se crea el registro de la solicitud)
            *   (En caso de que existan errores envia con las alertas a la vista NEW)
            */         
            if($alert == 0){
            
                /* Setea el usuario y estatus que tenia la solicitud antes de ser editada */
                $entity->setSolicitante($usuario);
                $entity->setEstatus($estatus);

                /* Inserta en BD */
                $em->persist($entity);
                $em->flush();

                /* Redirige a la funcion showAction() para mostrar el detalle de la solicitud editada */
                return $this->redirect($this->generateUrl('solicitudesapp_show', array('id' => $id)));
            }
        }        
        /* En caso de error en el formulario, envia al "Edit" nuevamente con las alertas */
        return $this->render('SolicitudesBundle:Solicitudes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }


################################################################################################################################
#                                                  CONTAR REPETIDOS
################################################################################################################################

    /**
    *   Cuenta los campos que estan repetidos en el array que se le envia
    *
    */
    function contar_repetidos($array)
    {
        $repetidos = 0; $cont=0;
        $ya_duplicados = array();
        foreach($array as $item)
        {
            for($u=0;$u<sizeof($array); $u++)
            {
                if($item == $array[$u] && !in_array($item, $ya_duplicados))
                {
                    ++$cont;
                }
            }
            
            if($cont >= 2)
            {
                array_push($ya_duplicados, $item);
                $repetidos++;
            }
            
            $cont = 0;
        }
        return $repetidos;
    }
    
################################################################################################################################
#                                                  VALIDACIONES DE COLLECTIONS
################################################################################################################################

    /**
    *   Valida los campos de Collections
    *   (Requerimientos, Responsables y Unidades)
    *
    */
    function validaciones($entity)
    {
        $alert = 0;
        /**
            * VALIDACIONES PARA LOS REQUERIMIENTOS
            *   (Se verifica si existen requerimientos creados, si el usuario hizo clic sobre "Agregar Requerimiento")
            *   (Se verifica que los requerimientos enviados no esten vacios o sean nulos)
            *   (Se envian mensajes de alerta en caso de existir algun error)
            */
            /* if para validar si se creo algun requerimiento */
            if(count($entity->getRequerimientos()) > 0){

                foreach ($entity->getRequerimientos() as $requerimiento) {
                    /* if para validar si el requerimiento esta vacio o enviado en Null*/
                    if(!empty($requerimiento->getRequerimiento()))
                    {
                        $requerimiento->setSolicitudes($entity);
                    }else{
                        $alert++;                        
                    }//fin if                    
                }//fin foreach

                /* Se envian mensajes de alerta si algun registro esta vacio o en Nulo*/
                if($alert == 1)
                {
                    $mensaje1 = "Existe 1 Requerimiento que se encuentra vacio, por favor verifique";
                    $this->get('session')->getFlashBag()->add('alert', $mensaje1);   
                }elseif ($alert > 1) {
                   $mensaje1 = "Existen ".$alert." Requerimientos que se encuentran vacios, por favor verifique";
                   $this->get('session')->getFlashBag()->add('alert', $mensaje1);
                }//fin if
            }else{
                $alert ++;
                $mensaje1 = "Los Requerimientos del sistema deben estar especificados en la solicitud";
                $this->get('session')->getFlashBag()->add('alert', $mensaje1);
            }//fin if
       
            /**
            * VALIDACIONES PARA LAS UNIDADES
            *   (se verifica que el usuario seleccionara alguna Unidad, hizo clic sobre "Agregar Unidad")
            *   (se verifica que no existan unidades repetidas dentro de las seleccionadas)
            *   (se envian mensajes de alerta en caso de existir algun error)
            */

            /* if para validar si se seleccionó alguna unidad */
            if(count($entity->getUnidades()) > 0){

                //se inicializan las variables que se usarán en este bloque
                $array_unidades = array(); 
                $i = 0;

                /* Se agrega en un array las unidades enviadas desde el formulario */
                foreach ($entity->getUnidades() as $unidad) {                    
                    $array_unidades[$i] = $unidad->getUnidad()->getId();
                    $i++;
                }

                /* Se llama a la funcion que cuenta los registros repetidos de un array */
                $repetidos = $this->contar_repetidos($array_unidades);  

                /* se valida si existen elementos repetidos */
                if($repetidos == 0)
                {
                    foreach ($entity->getUnidades() as $unidad) {
                        $unidad->setSolicitudes($entity);                                        
                    }
                }else
                {
                    $alert ++;
                    $mensaje2 = "Debe seleccionar Unidades diferentes, existen registros repetidos";
                    $this->get('session')->getFlashBag()->add('alert', $mensaje2);
                }//fin if
                
            }else{
                $alert++;
                $mensaje2 = "Las Unidades que harán uso de la aplicación deben estar especificadas en la solicitud";
                $this->get('session')->getFlashBag()->add('alert', $mensaje2);
            }//fin if                         
         


            /**
            * VALIDACIONES PARA LAS RESPONSABLES
            *   (se verifica que el usuario seleccionara algun Responsable de uso, hizo clic sobre "Agregar Responsable")
            *   (se verifica que no existan Responsables repetidos dentro de los que se seleccionó)
            *   (se envian mensajes de alerta en caso de existir algun error)
            */

            /* if para validar si se seleccionó algun Responsable */
            if(count($entity->getResponsables()) > 0){

                //se inicializan las variables que se usarán en este bloque
                $array_responsables = array(); 
                $i = 0;

                /* Se agrega en un array los responsables enviados desde el formulario */
                foreach ($entity->getResponsables() as $responsables) {                    
                    $array_responsables[$i] = $responsables->getResponsable()->getId();
                    $i++;
                }

                /* Se llama a la funcion que cuenta los registros repetidos de un array */
                $repetidos = $this->contar_repetidos($array_responsables);  

                /* se valida si existen elementos repetidos */
                if($repetidos == 0){
                    foreach ($entity->getResponsables() as $responsables) {
                        $responsables->setSolicitudes($entity);
                    }
                }else
                {
                    $alert ++;
                    $mensaje3 = "Debe seleccionar Responsables diferentes, existen registros repetidos";
                    $this->get('session')->getFlashBag()->add('alert', $mensaje3);
                }//fin if                    
            }else
            {
                $alert++;
                $mensaje3 = "Los Responsables deben estar especificados en la solicitud";
                $this->get('session')->getFlashBag()->add('alert', $mensaje3);
            }
            /* retorna el alert */
            return $alert;
    }

    /**
     * Deletes a Solicitudes entity.
     *
     */
    /*
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SolicitudesBundle:Solicitudes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Solicitudes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('solicitudesapp'));
    }
    */

    /**
     * Creates a form to delete a Solicitudes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('solicitudesapp_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }*/
}
