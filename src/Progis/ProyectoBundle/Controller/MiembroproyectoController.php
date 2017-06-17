<?php

namespace Progis\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\ProyectoBundle\Entity\Miembroproyecto;
use Progis\ProyectoBundle\Form\MiembroproyectoType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Miembroproyecto controller.
 *
 */
class MiembroproyectoController extends Controller
{
    
    public function seguridad($proyecto){
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        if(
                isset($rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]) and (
               $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true)
            
            or (isset($rolProyecto[$proyecto->getId()]) and ($rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true))
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }

    /**
     * Lists all Miembroproyecto entities.
     *
     */
    public function indexAction($idproyecto)
    {
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $entities = $em->getRepository('ProyectoBundle:Miembroproyecto')->findByProyecto($proyecto);
        $nivel=$proyecto->getNivelorganizacional();
        
        $this->seguridad($proyecto);
        
        return $this->render('ProyectoBundle:Miembroproyecto:index.html.twig', array(
            'entities' => $entities,
            'proyecto' => $proyecto
        ));
    }
    /**
     * Creates a new Miembroproyecto entity.
     *
     */
    public function createAction(Request $request,$idproyecto)
    {
     
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);

        $this->seguridad($proyecto);
        
        //obtengo los id de los espacios al cual pertenezco
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = new Miembroproyecto();
        $form = $this->createCreateForm($entity,$idproyecto,$perfil->getNivelorganizacional()->getId());
        $form->handleRequest($request);


        //obtengo la info del usuario seleccionado en el formulario
        $data=$form->getData();
        $usuarioMiembroEspacio=$data->getMiembroespacio();
        //fin 
        
        //validar si el usuario fue agregado como miembro
        $miembroProyecto = $em->getRepository('ProyectoBundle:Miembroproyecto')->findBy(array('miembroespacio'=>$usuarioMiembroEspacio,'proyecto'=>$idproyecto));
        if(!empty($miembroProyecto)):
            $this->get('session')->getFlashBag()->add('alert', 'Este usuario ya fue agregado como miembro de tu proyecto.');
            return $this->render('ProyectoBundle:Miembroproyecto:new.html.twig', array(
                'entity' => $entity,
                'proyecto'=>$proyecto,
                'form'   => $form->createView(),
            ));
        endif;

        if ($form->isValid()) {
            $entity->setProyecto($proyecto);
            $em->persist($entity);
            $em->flush();
            

            //envio correo
            $html= $this->renderView('ProyectoBundle:Correo:miembroProyecto.html.twig',array('miembroProyecto' => $entity));
            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Eres miembro de un nuevo proyecto','Progis_Informacion',array($entity->getMiembroespacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net'),$html);
            $this->get('mailer')->send($correo); 
            //fin envio correo
            
            
            $this->get('session')->getFlashBag()->add('notice', 'El usuario se ha creado con exito.');
            return $this->redirect($this->generateUrl('miembroproyecto_show', array('id' => $entity->getId())));
        }

        return $this->render('ProyectoBundle:Miembroproyecto:new.html.twig', array(
            'entity' => $entity,
            'proyecto'=>$proyecto,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Miembroproyecto entity.
     *
     * @param Miembroproyecto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Miembroproyecto $entity,$idproyecto,$idnivel)
    {
        $form = $this->createForm(new MiembroproyectoType($idnivel), $entity, array(
            'action' => $this->generateUrl('miembroproyecto_create',array('idproyecto'=>$idproyecto)),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Miembroproyecto entity.
     *
     */
    public function newAction($idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);

        $this->seguridad($proyecto);
        
        //obtengo los id de los espacios al cual pertenezco
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $entity = new Miembroproyecto();
        $form   = $this->createCreateForm($entity,$idproyecto,$perfil->getNivelorganizacional()->getId());
        
        

        return $this->render('ProyectoBundle:Miembroproyecto:new.html.twig', array(
            'entity' => $entity,
            'proyecto' => $proyecto,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Miembroproyecto entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Miembroproyecto')->find($id);
        $proyecto = $entity->getProyecto();

        $this->seguridad($proyecto);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroproyecto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getMiembroespacio()->getUsuario());
        
        return $this->render('ProyectoBundle:Miembroproyecto:show.html.twig', array(
            'entity'      => $entity,
            'proyecto' => $proyecto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Miembroproyecto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Miembroproyecto')->find($id);
        
        $this->seguridad($entity->getProyecto());
        
        //obtengo los id de los espacios al cual pertenezco
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $funcionModelo = $this->get('funcionModelo');
        $idNivelMiembroEspacio = $funcionModelo->idNivelMiembroEspacio($perfil);   
        
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroproyecto entity.');
        }

        $editForm = $this->createEditForm($entity,$perfil->getNivelorganizacional()->getId());
        $deleteForm = $this->createDeleteForm($id);

    
        
        return $this->render('ProyectoBundle:Miembroproyecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Miembroproyecto entity.
    *
    * @param Miembroproyecto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Miembroproyecto $entity,$nivel)
    {
        $form = $this->createForm(new MiembroproyectoType($nivel), $entity, array(
            'action' => $this->generateUrl('miembroproyecto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Miembroproyecto entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Miembroproyecto')->find($id);
        
        $this->seguridad($entity->getProyecto());
        
        //obtengo los id de los espacios al cual pertenezco
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $funcionModelo = $this->get('funcionModelo');
        $idNivelMiembroEspacio = $funcionModelo->idNivelMiembroEspacio($perfil);  
        
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroproyecto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity,$idNivelMiembroEspacio);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'El usuario se ha editado con exito.');
            return $this->redirect($this->generateUrl('miembroproyecto_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Miembroproyecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Miembroproyecto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyectoBundle:Miembroproyecto')->find($id);

            $this->seguridad($entity->getProyecto());
            
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Miembroproyecto entity.');
            }
            
            $idproyecto=$entity->getProyecto()->getId();

            $em->remove($entity);
            $em->flush();
            
        }
        
        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getMiembroespacio()->getUsuario());

        $this->get('session')->getFlashBag()->add('notice', 'El usuario se ha borrado con exito.');
        return $this->redirect($this->generateUrl('miembroproyecto',array('idproyecto'=>$idproyecto)));
    }

    /**
     * Creates a form to delete a Miembroproyecto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('miembroproyecto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
