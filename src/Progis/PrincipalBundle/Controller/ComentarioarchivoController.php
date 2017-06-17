<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\PrincipalBundle\Entity\Comentarioarchivo;
use Progis\PrincipalBundle\Form\ComentarioarchivoType;

use Progis\PrincipalBundle\Entity\ComentarioTicket;

class ComentarioarchivoController extends Controller
{
    
    public function listaAction($id,$entidad,$desde)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "select x from PrincipalBundle:Comentario".$entidad." x join x.comentarioarchivo ca where x.".strtolower($entidad)."= :".strtolower($entidad)." order by ca.fechahora DESC";
        $query = $em->createQuery($dql);
        $query->setParameter(strtolower($entidad),$id);
        $comentario = $query->getResult();
        if(empty($comentario))$comentario=null;
        
        
        $bundle=$entidad;
        if($bundle=='ProcesarActividad')$bundle='Proyecto';
        $info= $em->getRepository($bundle.'Bundle:'.$entidad)->find($id);
        
        return $this->render('PrincipalBundle:comentarioarchivo:lista.html.twig', array(
            'comentario' => $comentario,
            'desde'=>$desde,
            'entidad'=>$entidad,
            'id'=>$id,
            'info'=>$info
        ));
    } 
    
    public function newAction($id,$entidad,$desde)
    {        
        $em = $this->getDoctrine()->getManager();
        
        $bundle=$entidad;
        if($bundle=='ProcesarActividad')$bundle='Proyecto';
        $info= $em->getRepository($bundle.'Bundle:'.$entidad)->find($id);
        
        $entity = new Comentarioarchivo();
        $form   = $this->createCreateForm($entity,$id,$entidad,$desde);

        
        return $this->render('PrincipalBundle:comentarioarchivo:new.html.twig', array(
            'info' => $info,
            'form'   => $form->createView(),
            'id'=>$id,
            'desde'=>$desde,
            'entidad'=>$entidad
        ));
    }
    
    private function createCreateForm(Comentarioarchivo $entity,$id,$entidad,$desde)
    {
        $form = $this->createForm(new ComentarioarchivoType(), $entity, array(
            'action' => $this->generateUrl('progis_comentarioarchivo_create',array('id'=>$id,'entidad'=>$entidad,'desde'=>$desde)),
            'method' => 'POST',
        ));

        return $form;
    }
    
    public function createAction(Request $request, $id,$entidad,$desde)
    {
        $em = $this->getDoctrine()->getManager();
        
        //definiciones
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            $bundle=$entidad;
            if($bundle=='ProcesarActividad')$bundle='Proyecto';
            $info= $em->getRepository($bundle.'Bundle:'.$entidad)->find($id);
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
        //fin definiciones
            
        $entity = new Comentarioarchivo();
        $form   = $this->createCreateForm($entity,$id,$entidad,$desde);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            //procesar archivo
            if($form['file']->getData()):
                
                $file=$form['file']->getData();
                
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $nombre=$file->getClientOriginalName();
                $nombreLargo=explode(".", $nombre);
                $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
                $extension = $nombreLargo[1];
                
                //validaciones
                    $extensiones=array('jpg','jpeg','png','gif','doc','odt','xls','xlsx','docx','pdf','ppt','pptx','zip','rar','pod');
                    if (!in_array($extension,$extensiones)) {
                        $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                        return $this->render('PrincipalBundle:comentarioarchivo:new.html.twig', array(
                            'info' => $info,
                            'form'   => $form->createView(),
                            'desde' => $desde,
                            'entidad'=>$entidad,
                            'id'=>$id
                        ));
                    }
                //fin
                    
                if($file->move('/var/www/uploads/progis/',$nombre.'_'.\date("Gis").'.'.$extension) )
                {
                    $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
                }
                
            endif;
            //fin procesar archivo
            

            $entity->setFechahora($fa);
            $entity->setResponsable($perfil);
            $em->persist($entity);
            $em->flush();
            
            //comentario ya guardado
            $comentarioEntidad=$entity;
            $nombreEntidad='Progis\PrincipalBundle\Entity\Comentario'.$entidad;
            $set="set".$entidad;

            $entity = new $nombreEntidad();
            $entity->$set($info);
            $entity->setComentarioarchivo($comentarioEntidad);
            $em->persist($entity);
            $em->flush();

            
            //envio correo
            $para=array();
            
            //preparo plantilla
            $html= $this->renderView('PrincipalBundle:Correo:comentario'.$entidad.'.html.twig',array('entity' => $entity,'desde'=>'desde'));
            //busco los responsables
            if($entidad=='Proyecto'):
                $dql = "select mp from ProyectoBundle:Miembroproyecto mp join mp.miembroespacio me where mp.proyecto= :idproyecto and me.activo=true";
                $query = $em->createQuery($dql);
                $query->setParameter('idproyecto',$id);
                $miembroProyecto=$query->getResult();

                if(!empty($miembroProyecto)):
                    foreach ($miembroProyecto as $mp) {
                        $para[]=$mp->getMiembroespacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net';
                    }
                endif;
            elseif($entidad=='ProcesarActividad'):
                $dql = "select mp from ProyectoBundle:Miembroproyecto mp join mp.miembroespacio me where mp.proyecto= :idproyecto and me.activo=true";
                $query = $em->createQuery($dql);
                $query->setParameter('idproyecto',$info->getObjetivo()->getProyecto());
                $miembroProyecto=$query->getResult();

                //$miembroProyecto= $em->getRepository('ProyectoBundle:Miembroproyecto')->findByProyecto($info->getObjetivo()->getProyecto());
                if(!empty($miembroProyecto)):
                    foreach ($miembroProyecto as $mp) {
                        $para[]=$mp->getMiembroespacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net';
                    }
                endif;
                //$para[]=$info->getResponsable()->getMiembroespacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net';
            elseif($entidad=='Ticket'):
                $procesarTicket= $em->getRepository('TicketBundle:ProcesarTicket')->findByTicket($id);
                if(!empty($procesarTicket)):
                    $para[]=$procesarTicket[0]->getResponsable()->getUsuario()->getUser()->getUsername().'@telesurtv.net';
                endif;
            endif;
            
            if(!empty($para)):
                $funcion=$this->get("funcionModelo");
                $correo=$funcion->correo('Nuevo comentario y/o archivo','Progis_Informacion',$para,$html);
                $this->get('mailer')->send($correo); 
            endif;
            //fin envio correo

        
            return $this->redirect($this->generateUrl('progis_comentarioarchivo_lista', array('id' => $id,'entidad'=>$entidad,'desde'=>$desde)));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('PrincipalBundle:comentarioarchivo:new.html.twig', array(
            'info' => $info,
            'form'   => $form->createView(),
            'id'=>$id,
            'desde'=>$desde,
            'entidad'=>$entidad,
        ));
    }
    
   

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Comentario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comentario entity.');
        }
        $proyecto = $entity->getProyectoId()->getId();
       
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Comentario:show.html.twig', array(
            'entity'      => $entity,
            'proyecto'    => $proyecto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Comentario entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Comentario')->find($id);
        $proyecto = $entity->getProyectoId()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comentario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Comentario:edit.html.twig', array(
            'entity'      => $entity,
            'proyecto'    => $proyecto,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Comentario entity.
    *
    * @param Comentario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comentario $entity)
    {
        $form = $this->createForm(new ComentarioType(), $entity, array(
            'action' => $this->generateUrl('comentarioproyecto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Comentario entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Comentario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comentario entity.');
        }

        $proyect = $entity->getProyectoId()->getId();
        $proyectId = $em->getRepository('ProyectoBundle:Proyecto')->find($proyect);
        $fechaRegistro = $entity->getFechaRegistro();


        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setFechaRegistro($fechaRegistro);
            $entity->setProyectoId($proyectId);

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Se modificó el comentario satisfactoriamente');
            return $this->render('ProyectoBundle:Comentario:show.html.twig', array(
            'entity'      => $entity,
            'proyecto'    => $proyect,
            'delete_form' => $deleteForm->createView(),
        ));
        }

        return $this->render('ProyectoBundle:Comentario:edit.html.twig', array(
            'entity'      => $entity,
            'proyecto'    => $proyect,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Comentario entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyectoBundle:Comentario')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comentario entity.');
            }

            $proyecto = $entity->getProyectoId()->getId();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('comentarioproyecto', array('proyecto' => $proyecto)));
    }

    /**
     * Creates a form to delete a Comentario entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comentarioproyecto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
