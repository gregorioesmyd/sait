<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\PrincipalBundle\Entity\Miembroespacio;
use Progis\PrincipalBundle\Form\MiembroespacioType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Miembroespacio controller.
 *
 */
class MiembroespacioController extends Controller
{
    
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }
    
    public function indexAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        //busco a todas las personas que son miembros y tienen esos mismos niveles        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no where no.id in (:idnivel)";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$perfil->getNivelorganizacional());
        $miembroespacio=$query->getResult();
        

        //desactivo egresados
        foreach ($miembroespacio as $me) {
            if($me->getUsuario()->getUser()->getIsActive()==false):
                $me->setActivo(false);
                $me->setMostrarEnReporte(false);
                $em->flush();

            endif;
        }
        

        $actividad=null;
        $ticket=null;
        if(!empty($miembroespacio)):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join x.responsable mp where mp.miembroespacio in (:usuario) and x.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('usuario',$miembroespacio);
            $actividades = $query->getResult();

            $actividad=array();
            foreach ($actividades as $a) {
                $actividad[$a->getResponsable()->getMiembroespacio()->getId()]=$a;
            }

            $dql = "select x from TicketBundle:ProcesarTicket x join x.ticket t  where x.responsable in (:usuario) and x.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('usuario',$miembroespacio);
            $tickets = $query->getResult();
            $ticket=array();
            foreach ($tickets as $t) {
                $ticket[$t->getResponsable()->getId()]=$t;
            }
        endif;

        
        
        return $this->render('PrincipalBundle:Miembroespacio:index.html.twig', array(
            'entities' => $miembroespacio,
            'actividad'=>$actividad,
            'ticket'=>$ticket
        ));
    }

    
    public function createAction(Request $request)
    {
        $this->seguridad();
        
        $entity = new Miembroespacio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //valido si asigno rol o no
        $data=$form->getData();
        
        $a=0;
        foreach ($data->getRolgeneral() as $v) {$a=1;}

        $b=0;
        if($data->getPoseerolgeneral()==true and $a==0){
            $b=1;
            $this->get('session')->getFlashBag()->add('alert', 'Debe seleccionar un rol si marcó la opción "Asignar rol general".');
        }
            
        if ($form->isValid() and $b==0) {   
            
            $em = $this->getDoctrine()->getManager();
            
            //obtengo info del usuario actual
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            //fin
                        
            //obtengo la info del usuario seleccionado en el formulario
            $usuarioMiembroEspacio=$data->getUsuario();
            //fin 

            //validar si el usuario fue agregado como miembro
            $miembroEspacio = $em->getRepository('PrincipalBundle:Miembroespacio')->findBy(array('usuario'=>$usuarioMiembroEspacio,'nivelorganizacional'=>$perfil->getNivelorganizacional()));
            if(!empty($miembroEspacio)):
                $this->get('session')->getFlashBag()->add('alert', 'Este usuario ya fue agregado como miembro de tu espacio.');
                
                return $this->render('PrincipalBundle:Miembroespacio:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            endif;
            //fin
            
            $entity->setNivelorganizacional($perfil->getNivelorganizacional());
            $entity->setResponsable($perfil);
            $entity->setMostrarEnReporte(true);
            
            $em->persist($entity);
                            
            $em->flush();

            return $this->redirect($this->generateUrl('miembroespacio_show', array('id' => $entity->getId())));
        }

        return $this->render('PrincipalBundle:Miembroespacio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Miembroespacio entity.
     *
     * @param Miembroespacio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Miembroespacio $entity)
    {
        $form = $this->createForm(new MiembroespacioType(), $entity, array(
            'action' => $this->generateUrl('miembroespacio_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Miembroespacio entity.
     *
     */
    public function newAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        
        $entity = new Miembroespacio();
        $form   = $this->createCreateForm($entity);        
        
        return $this->render('PrincipalBundle:Miembroespacio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),

        ));
    }

    /**
     * Finds and displays a Miembroespacio entity.
     *
     */
    public function showAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Miembroespacio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroespacio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getUsuario());

        return $this->render('PrincipalBundle:Miembroespacio:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Miembroespacio entity.
     *
     */
    public function editAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Miembroespacio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroespacio entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        
        
        return $this->render('PrincipalBundle:Miembroespacio:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Miembroespacio entity.
    *
    * @param Miembroespacio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Miembroespacio $entity)
    {
        $form = $this->createForm(new MiembroespacioType(), $entity, array(
            'action' => $this->generateUrl('miembroespacio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Miembroespacio entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->seguridad();
        
        $roles=null;
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Miembroespacio')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Miembroespacio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        //valido si asigno rol o no
        $data=$editForm->getData();
        
        $a=0;
        foreach ($data->getRolgeneral() as $v) {$a=1;}

        $b=0;
        if($data->getPoseerolgeneral()==true and $a==0){
            $b=1;
            $this->get('session')->getFlashBag()->add('alert', 'Debe seleccionar un rol si marcó la opción "Asignar rol general".');
        }
            
        if ($editForm->isValid() and $b==0) {   
            //obtengo info del usuario actual
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            //fin

            $entity->setNivelorganizacional($perfil->getNivelorganizacional());
            $em->flush();
            

            $this->get('session')->getFlashBag()->add('notice', 'Miembro editado con exito.');
            return $this->redirect($this->generateUrl('miembroespacio_show', array('id' => $id)));
        }


        if($b==0)
        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('PrincipalBundle:Miembroespacio:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Miembroespacio entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PrincipalBundle:Miembroespacio')->find($id);
            
            //busco si existe en miembroproyecto
            $dql = " select x from ProyectoBundle:Miembroproyecto x 
                     join x.miembroespacio me
                     where me.usuario= :usuario";

            $query = $em->createQuery($dql);
            $query->setParameter('usuario',$entity->getUsuario());
            $miembroProyecto = $query->getResult();

            if(!empty($miembroProyecto)):
                $this->get('session')->getFlashBag()->add('alert', 'No puede eliminar a este usuario ya que es miembro de uno o varios proyectos.');
                return $this->redirect($this->generateUrl('miembroespacio_show', array('id' => $entity->getId())));
            endif;
            
        
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Miembroespacio entity.');
            }

            
            $em->remove($entity);
            $em->flush();
        }

        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getUsuario());
        
        return $this->redirect($this->generateUrl('miembroespacio'));
    }

    /**
     * Creates a form to delete a Miembroespacio entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('miembroespacio_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
