<?php

namespace Progis\ProyectoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Administracion\UsuarioBundle\Resources\Misclases\Funcion;
use Progis\ProyectoBundle\Entity\ProcesarActividad;
use Progis\ProyectoBundle\Form\ProcesarActividadType;

use Progis\PrincipalBundle\Entity\Comentarioarchivo;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProcesarActividadController extends Controller
{
    public function seguridadNuevoEditDel($proyecto){
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        if(
               $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_TECNICO']==true  
                
            //coloco el isset porque puede que le hayan colocado rol admin general y no necesariamente se le asigno el rol al proyecto directamente    
            or (isset($rolProyecto[$proyecto->getId()]) and ($rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true
            or $rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_TECNICO']==true))
                
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }

    
    
    public function cuentaRegresiva($em, $objetivo){
            
        $dql = "select p from ProyectoBundle:ProcesarActividad p where p.objetivo=:objetivo and p.ubicacion=2 ";
        $query = $em->createQuery($dql);
        $query->setParameter('objetivo',$objetivo);
        $procesarEnProceso = $query->getResult();

        $cuentaregresiva=array();
        $ubicacionModelo = $this->get('ubicacionModelo');
        
        foreach ($procesarEnProceso as $pep) {
            $cuentaregresiva[$pep->getId()]=$ubicacionModelo->calculaCuentaRegresiva($pep,'proyecto'); 
        }
        return $cuentaregresiva;
    }
    
    //nivel del usuario actual
    public function idnivelusuario() {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        
        $idnivel=array();
        
        if ($this->get('security.context')->isGranted('ROLE_PROGIS_SUPERADMIN')) {
            $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);    
            $idnivel[]=$perfil->getNivelorganizacional()->getId();  
        }
        
        $miembroEspacio =  $em->getRepository('PrincipalBundle:Miembroespacio')->findByUsuario($idusuario);
        foreach ($miembroEspacio as $me) {
            $idnivel[]=$me->getNivelorganizacional()->getId();
        }
        
        return $idnivel;
    }
    
    public function nivelusuarios() {
        $em = $this->getDoctrine()->getManager();
         //busco todos los usuarios pertenecientes a la unidad
        $idnivelusuario=  $this->idnivelusuario();
        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.id in (:idnivel) and u.isActive=true order by p.primerNombre, p.primerApellido ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idnivelusuario);
        $a=$query->getResult();
        return $a;       
    }
    
    public function procesar($idobjetivo,$perfil) {
        
        $em = $this->getDoctrine()->getManager();
        $dql = "select p from ProyectoBundle:ProcesarActividad p join p.responsable mp join mp.miembroespacio me where p.objetivo= :idobjetivo order by p.ordenPriometa asc, p.orden asc, p.id ASC ";
        $query = $em->createQuery($dql);
        $query->setParameter('idobjetivo',$idobjetivo);
        $procesar = $query->getResult();
        return $procesar;
        
    }
    
    public function procesarActividadAction($idobjetivo)
    {
        $smsModal=null;
        
        $em = $this->getDoctrine()->getManager();

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $objetivo=$em->getRepository('ProyectoBundle:Objetivo')->find($idobjetivo);
        $procesar=  $this->procesar($idobjetivo,$perfil);

        $cuentaregresiva=  $this->cuentaregresiva($em,$objetivo);

        //cantidad de comentarios en actividades de los objetivos
        $objetivoModelo = $this->get('objetivoModelo');
        $cantidadComentarioActividad=$objetivoModelo->cantidadComentarioActividad($procesar);

        return $this->render('ProyectoBundle:Procesar:procesarActividad.html.twig',array('cantidadComentarioActividad'=>$cantidadComentarioActividad,'procesar'=>$procesar,'cuentaregresiva'=>$cuentaregresiva,'objetivo'=>$objetivo,'smsModal'=>$smsModal));
        
    }
    
    
    
    
    
    public function newAction($idobjetivo)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $objetivo = $em->getRepository('ProyectoBundle:Objetivo')->find($idobjetivo);
        
        $dql = "select p from ProyectoBundle:Miembroproyecto p join p.miembroespacio me where p.proyecto= :proyecto and me.usuario= :usuario";
        $query = $em->createQuery($dql);
        $query->setParameter('proyecto',$objetivo->getProyecto());
        $query->setParameter('usuario',$perfil);
        $idmiembroproyecto = $query->getResult();
        if(!empty($idmiembroproyecto))$idmp=$idmiembroproyecto[0]->getId();else $idmp=null;
        
        $this->seguridadNuevoEditDel($objetivo->getProyecto());
        
        //obtengo los id de los espacios al cual pertenezco
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $funcionModelo = $this->get('funcionModelo');
        $idNivelMiembroEspacio = $funcionModelo->idNivelMiembroEspacio($perfil);   
        
        $entity = new ProcesarActividad();
        $form   = $this->createCreateForm($entity,$idobjetivo,$objetivo->getProyecto());
        return $this->render('ProyectoBundle:Procesar:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'objetivo'=>$objetivo,
            'idmp'=>$idmp
        ));
    }
    
    public function createAction(Request $request,$idobjetivo)
    {
        $dataRequest=$request->get('frontend_proyectobundle_procesaractividad');
        
        $em = $this->getDoctrine()->getManager();
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $objetivo = $em->getRepository('ProyectoBundle:Objetivo')->find($idobjetivo);
        $nivel=$objetivo->getProyecto()->getNivelorganizacional();
        
            
        $entity = new ProcesarActividad();
        $form = $this->createCreateForm($entity,$idobjetivo,$objetivo->getProyecto());
        $data=$form->getData();
        
        $form->handleRequest($request);

        $a=0;
        if($data->getTipotiempo()==2 and $data->getTiempoestimado()>8):
            $this->get('session')->getFlashBag()->add('alert', 'La cantidad de horas máxima permitida es 8.');
            $a=1;
        elseif($data->getTipotiempo()==3 and $data->getTiempoestimado()>480):
            $this->get('session')->getFlashBag()->add('alert', 'La cantidad de minutos máximo permitido es 480.');
            $a=1;
        endif;
            
        
        if ($form->isValid() and $a==0) {

        
            $entity->setUbicacion(1);
            $entity->setUbicacionPriometa(1);
            $entity->setObjetivo($objetivo);
            $entity->setCorreoretardo(false);
            $entity->setFechacreacion($fa);
            $entity->setRegistradopor($perfil);
            $em->persist($entity);
            //$em->flush();

            $objetivoModelo = $this->get('objetivoModelo');
            $objetivoModelo->porcentajeobjetivo($idobjetivo,$this->get('proyectoModelo'));
            $objetivoModelo->estatusobjetivo($idobjetivo,$this->get('proyectoModelo'));

            //envio de correo
            $html= $this->renderView('ProyectoBundle:Correo:nuevaActividad.html.twig',array('entity' => $entity));
            $para[]=$entity->getResponsable()->getMiembroespacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net';

            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Nueva actividad','Progis_Informacion',$para,$html);
            $this->get('mailer')->send($correo); 
            //fin envio correo
            $this->get('session')->getFlashBag()->add('notice', 'Se ha creado la actividad exitosamente.');
            return $this->redirect($this->generateUrl('actividad_show', array('id' => $entity->getId())));
        }

        $dql = "select p from ProyectoBundle:Miembroproyecto p join p.miembroespacio me where p.proyecto= :proyecto and me.usuario= :usuario";
        $query = $em->createQuery($dql);
        $query->setParameter('proyecto',$objetivo->getProyecto());
        $query->setParameter('usuario',$perfil);
        $idmiembroproyecto = $query->getResult();
        if(!empty($idmiembroproyecto))$idmp=$idmiembroproyecto[0]->getId();else $idmp=null;
        
        return $this->render('ProyectoBundle:Procesar:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'objetivo'=>$objetivo,
            'idmp'=>$idmp
        ));
    }

    /**
     * Finds and displays a Actividad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('alert', 'Esta actividad fue eliminada.');
            return $this->redirect($this->generateUrl('progis_principal_homepage'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Procesar:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function createCreateForm(ProcesarActividad $entity,$idobjetivo,$proyecto)
    {
        $form = $this->createForm(new ProcesarActividadType($proyecto), $entity, array(
            'action' => $this->generateUrl('actividad_create',array('idobjetivo'=>$idobjetivo)),
            'method' => 'POST',
        ));

        return $form;
    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $entity = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        
        $this->seguridadNuevoEditDel($entity->getObjetivo()->getProyecto());

        $nivel=$entity->getObjetivo()->getProyecto()->getNivelorganizacional();
        
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }
        $editForm = $this->createEditForm($entity,$entity->getObjetivo()->getProyecto());
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Procesar:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Actividad entity.
    *
    * @param Actividad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProcesarActividad $entity,$proyecto)
    {
        $form = $this->createForm(new ProcesarActividadType($proyecto), $entity, array(
            'action' => $this->generateUrl('actividad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Actividad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $entity = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        $ubicacion=$entity->getUbicacion();
        $objetivo=$entity->getObjetivo();
        $tipotiempo=$entity->getTipotiempo();
        $tiempoestimado=$entity->getTiempoestimado();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity,$entity->getObjetivo()->getProyecto());
        $editForm->handleRequest($request);

        $data=$editForm->getData();
        $a=0;
        if($data->getTipotiempo()==2 and $data->getTiempoestimado()>8):
            $this->get('session')->getFlashBag()->add('alert', 'La cantidad de horas máxima permitida es 8.');
            $a=1;
        elseif($data->getTipotiempo()==3 and $data->getTiempoestimado()>480):
            $this->get('session')->getFlashBag()->add('alert', 'La cantidad de minutos máximo permitido es 480.');
            $a=1;
        endif;
        
        if ($editForm->isValid() and $a==0) {
            $entity->setUbicacion($ubicacion);
            $entity->setObjetivo($objetivo);
            
            
            if($entity->getTiemporeal()!=null){
                $entity->setTiempoestimado($tiempoestimado);
                $entity->setTipotiempo($tipotiempo);
            }
            
            
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Se actualizó la actividad exitosamente.');
            return $this->redirect($this->generateUrl('actividad_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Procesar:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Actividad entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
            
            $this->seguridadNuevoEditDel($entity->getObjetivo()->getProyecto());
            
            $idobjetivo=$entity->getObjetivo()->getId();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Actividad entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            $objetivoModelo = $this->get('objetivoModelo');
            $objetivoModelo->porcentajeobjetivo($idobjetivo,$this->get('proyectoModelo'));
            $objetivoModelo->estatusobjetivo($idobjetivo,$this->get('proyectoModelo'));
        }

        $this->get('session')->getFlashBag()->add('notice', 'Se eliminó la actividad exitosamente.');
        return $this->redirect($this->generateUrl('proyecto_procesarActividad',array('idobjetivo'=>$idobjetivo)));
    }

    /**
     * Creates a form to delete a Actividad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actividad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
    
 public function JustificarActividadAction(Request $request,$idactividad)
    {   
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        /*datos para procesar Actividad*/
        $actividad=$em->getRepository('ProyectoBundle:ProcesarActividad')->find($idactividad);
        $objetivo=$actividad->getObjetivo();
        $procesar=  $this->procesar($objetivo->getId(),$perfil);
        $cuentaregresiva=  $this->cuentaregresiva($em,$objetivo->getId());
        /*fin*/

        if($request->get("justificacion")==null):

            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La justificación no debe estar en blanco.";
            return $this->render('ProyectoBundle:Procesar:procesarActividad.html.twig',array('procesar'=>$procesar,'cuentaregresiva'=>$cuentaregresiva,'objetivo'=>$objetivo,'smsModal'=>$smsModal));

        elseif(strlen($request->get("justificacion"))<=10):
            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La justificación debe contener al menos 10 caracteres.";
            return $this->render('ProyectoBundle:Procesar:procesarActividad.html.twig',array('procesar'=>$procesar,'cuentaregresiva'=>$cuentaregresiva,'objetivo'=>$objetivo,'smsModal'=>$smsModal));

        else:
            $coment=new Comentarioarchivo;
        
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
            
            $coment->setComentario($request->get("justificacion"));
            $coment->setResponsable($perfil);
            $coment->setFechahora($fa);
            $coment->setTipo(4);
            $em->persist($coment);
            $em->flush();
            
            $comentarioEntidad=$coment;
            
            $entity = new \Progis\PrincipalBundle\Entity\ComentarioProcesarActividad();
            $entity->setProcesaractividad($actividad);
            $entity->setComentarioarchivo($coment);
            $em->persist($entity);
            $em->flush();
            
            
            //cambio la justificacion a false
            $procesarActividad = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($idactividad);
            $procesarActividad->setJustificacion(false);
            $em->flush();
            
            
            $this->get('session')->getFlashBag()->add('notice', 'La justificación se ha realizado con exito');
            return $this->redirect($this->generateUrl('progis_comentarioarchivo_lista',array("id"=>$idactividad,"entidad"=>"ProcesarActividad",'desde'=>'Actividad')));
            
        endif;
    }
    
    
}
