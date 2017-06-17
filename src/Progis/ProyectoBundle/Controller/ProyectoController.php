<?php

namespace Progis\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Twig_Environment;

use Progis\ProyectoBundle\Entity\Proyecto;
use Progis\ProyectoBundle\Form\ProyectoType;

use Progis\ProyectoBundle\Entity\Proyectogeneral;
use Progis\ProyectoBundle\Form\ProyectogeneralType;

use Administracion\UsuarioBundle\Resources\Misclases\Funcion;
use Progis\ProyectoBundle\Resources\Misclases\Proyectofunciones;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * Proyecto controller.
 *
 */
class ProyectoController extends Controller
{
    public function seguridad() {

        $admin=false;if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolProyecto($session,$admin);
    }

    public function seguridadNuevo(){
        $session = $this->getRequest()->getSession();
        $rolGeneral=$session->get('rolGeneral');
        if(
               $rolGeneral['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneral['ROLE_PROGIS_PROYECTO_TECNICO']==true 
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }
    public function seguridadEditDelete($proyecto){
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        if(
               isset($rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]) and (
               $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_TECNICO']==true)
            
            or (isset($rolProyecto[$proyecto->getId()]) and ($rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_TECNICO']==true
            or $rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true))
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }

  
    
    //LISTA DE PROYECTOST
    public function indexAction()
    {
        $this->seguridad();

        //definiciones
            $em = $this->getDoctrine()->getManager();
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            //para obtener los usuarios del espacio de trabajo
            $funcionModelo = $this->get('funcionModelo');
            $idNivelMiembroEspacios=$funcionModelo->idNivelMiembroEspacio($perfil);
        //fin

        $proyectoModelo = $this->get('proyectoModelo');
        $funcionModelo = $this->get('funcionModelo');
        $session = $this->getRequest()->getSession();
        $entities=$proyectoModelo->listaProyecto($idusuario,$funcionModelo,$session,$this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'));

        $niveles=array();
        foreach ($entities as $n) {
            $no=$n->getNivelorganizacional();
            $niveles[$no->getId()]=$no;
        }

        //la cantidad total de objetivos de un proyecto
        $objetivoModelo = $this->get('objetivoModelo');
        $proyectoModelo = $this->get('proyectoModelo');
        $totalObjetivo=$objetivoModelo->totalobjetivo($entities);
        $miembroProyecto=$proyectoModelo->miembroProyecto($entities);
        //fin
        //cantidad de comentarios de un proyecto
        $cantidadComentarioProyecto=$proyectoModelo->cantidadComentarioProyecto($entities);
        
        return $this->render('ProyectoBundle:Proyecto:index.html.twig', array(
            'entities' => $entities,
            'totalobjetivo'=>$totalObjetivo,
            'miembrosDeProyectos'=>$miembroProyecto,
            'niveles'=>$niveles,
            'cantidadComentarioProyecto'=>$cantidadComentarioProyecto
        ));
    }
    
    public function newAction()
    {
        $this->seguridadNuevo();
        
        $em = $this->getDoctrine()->getManager();

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = new Proyecto();
        $form   = $this->createCreateForm($entity);

        return $this->render('ProyectoBundle:Proyecto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Creates a new Proyecto entity.
     *
     */
    public function createAction(Request $request)
    {
        $this->seguridadNuevo();
        
        $em = $this->getDoctrine()->getManager();
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $entity = new Proyecto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //guardo valores por defecto
            $entity->setEstatus(1);
            $entity->setPorcentaje(0);
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            $entity->setNivelorganizacional($perfil->getNivelorganizacional());
            $entity->setFechacreacion($fa);
            $entity->setInformegestion(false);
            $em->persist($entity);
            $em->flush();

            //$bitacoraModelo = $this->get('bitacoraModelo');
            //$bitacoraModelo->guarda_bitacora(2,'Ha creado el proyecto "'.$entity->getNombre().'"',$perfil,$entity->getId(),$perfil->getNivelorganizacional());
        
            $this->get('session')->getFlashBag()->add('notice', 'Proyecto creado exitosamente.');
            return $this->redirect($this->generateUrl('proyecto_show', array('id' => $entity->getId())));
        }
        
        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('ProyectoBundle:Proyecto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    

    private function createCreateForm(Proyecto $entity)
    {
        $form = $this->createForm(new ProyectoType(), $entity, array(
            'action' => $this->generateUrl('proyecto_create'),
            'method' => 'POST',
        ));
        return $form;
    }
    
    private function createEditForm(Proyecto $entity)
    {
        $form = $this->createForm(new ProyectoType(), $entity, array(
            'action' => $this->generateUrl('proyecto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $em->getRepository('ProyectoBundle:Proyecto')->find($id);

        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        
        if(
            isset($rolGeneralNivel[$entity->getNivelorganizacional()->getId()]) and(
               $rolGeneralNivel[$entity->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneralNivel[$entity->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_TECNICO']==true 
            or $rolGeneralNivel[$entity->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_CONSULTA']==true)

            or (isset($rolProyecto[$entity->getId()]) and ($rolProyecto[$entity->getId()]['ROLE_PROGIS_PROYECTO_ESP_CONSULTA']==true
            or $rolProyecto[$entity->getId()]['ROLE_PROGIS_PROYECTO_ESP_TECNICO']==true
            or $rolProyecto[$entity->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true))
                
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
        
        $miembroProyecto = $em->getRepository('ProyectoBundle:Miembroproyecto')->findByProyecto($entity);

        
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('alert', 'Este proyecto fue eliminado.');
            return $this->redirect($this->generateUrl('progis_principal_homepage'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Proyecto:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'miembroProyecto'=>$miembroProyecto
        ));
    }

    /**
     * Displays a form to edit an existing Proyecto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $entity = $em->getRepository('ProyectoBundle:Proyecto')->find($id);
        $nivel=$entity->getNivelorganizacional();
        
        $this->seguridadEditDelete($entity);
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proyecto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Proyecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    


    /**
    * Creates a form to edit a Proyecto entity.
    *
    * @param Proyecto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */

    

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $entity = $em->getRepository('ProyectoBundle:Proyecto')->find($id);

        $this->seguridadEditDelete($entity);
        
        //guardo el valor de las variables ya que no se envian por formulario
        $estatus=$entity->getEstatus();
        $porcentaje=$entity->getPorcentaje();
        $fechainicio=$entity->getFechainicioestimada();
        $fechafin=$entity->getFechafinestimada();
        $nivelorg=$entity->getNivelorganizacional();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proyecto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setEstatus($estatus);
            $entity->setPorcentaje($porcentaje);
            $entity->getFechainicioestimada($fechainicio);
            $entity->setFechafinestimada($fechafin);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Proyecto editado exitosamente.');
            return $this->redirect($this->generateUrl('proyecto_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Proyecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes a Proyecto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Proyecto')->find($id);
        $this->seguridadEditDelete($entity);
        
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        
        if ($form->isValid()) {

            $objetivos=$em->getRepository('ProyectoBundle:Objetivo')->findByProyecto($entity->getId());

            if(!empty($objetivos)){
                $this->get('session')->getFlashBag()->add('alert', 'No puede borrar el proyecto porque tiene objetivos asignadas.');
            }else{

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find Proyecto entity.');
                }

                $em->remove($entity);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('notice', 'Se ha borrado el proyecto "'.ucfirst($entity->getNombre()).'" exitosamente.');
            }
        }
        return $this->redirect($this->generateUrl('proyecto'));
    }

    /**
     * Creates a form to delete a Proyecto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proyecto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
