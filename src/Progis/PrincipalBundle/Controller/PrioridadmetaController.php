<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Prioridadmeta controller.
 *
 */
class PrioridadmetaController extends Controller
{
    
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }
    
    public function createAction(Request $request)
    {
        $this->seguridad();
        
        $entity = new Prioridadmeta();
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
            $miembroEspacio = $em->getRepository('PrincipalBundle:Prioridadmeta')->findBy(array('usuario'=>$usuarioMiembroEspacio,'nivelorganizacional'=>$perfil->getNivelorganizacional()));
            if(!empty($miembroEspacio)):
                $this->get('session')->getFlashBag()->add('alert', 'Este usuario ya fue agregado como miembro de tu espacio.');
                
                return $this->render('PrincipalBundle:Prioridadmeta:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            endif;
            //fin
            
            $entity->setNivelorganizacional($perfil->getNivelorganizacional());
            $entity->setResponsable($perfil);
            
            $em->persist($entity);
                            
            $em->flush();

            return $this->redirect($this->generateUrl('miembroespacio_show', array('id' => $entity->getId())));
        }

        return $this->render('PrincipalBundle:Prioridadmeta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Prioridadmeta entity.
     *
     * @param Prioridadmeta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Prioridadmeta $entity)
    {
        $form = $this->createForm(new PrioridadmetaType(), $entity, array(
            'action' => $this->generateUrl('miembroespacio_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    public function queryActividad($data,$entity){
        
        $em = $this->getDoctrine()->getManager();
        
        //si no selecciono nada
        if(empty($data) or (!empty($data) and $data['proyecto']=='t') and $data['objetivo']=='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x where x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('responsable',$entity);
            
        //si selecciono un proyecto
        elseif($data['proyecto']!='t' and $data['objetivo']=='t'):    
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p where p.id= :idp and x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('responsable',$entity);
            $query->setParameter('idp',$data['proyecto']);  
            
        //si selecciono un proyecto y un objetivo
        elseif($data['proyecto']!='t' and $data['objetivo']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p where o.id= :ido and p.id= :idp and x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('responsable',$entity);
            $query->setParameter('idp',$data['proyecto']); 
            $query->setParameter('ido',$data['objetivo']); 
            
        //si lecciono solo un objetivo
        elseif($data['proyecto']=='t' and $data['objetivo']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p where o.id= :ido and x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('responsable',$entity);
            $query->setParameter('ido',$data['objetivo']); 
        endif;
        

        $actividad = $query->getResult();
        return $actividad;
    }
    
    public function filtro($data,$entity){
        $arrayFiltro['proyecto']=array();
        $arrayFiltro['objetivo']=array();
        
        $em = $this->getDoctrine()->getManager();
        $dql = "select x from ProyectoBundle:ProcesarActividad x where x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('responsable',$entity);
        $general = $query->getResult();
        
        if(!empty($data) and $data['proyecto']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p where p.id=:idp and x.responsable= :responsable and x.ubicacionPriometa=1 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('responsable',$entity);
            $query->setParameter('idp',$data['proyecto']);  
            $filtroPorProyecto = $query->getResult();
        endif;

        foreach ($general as $a) {
            $arrayFiltro['proyecto'][$a->getObjetivo()->getProyecto()->getId()]=$a->getObjetivo()->getProyecto()->getNombre();
            if((!empty($data) and $data['proyecto']=='t') or empty($data))
            $arrayFiltro['objetivo'][$a->getObjetivo()->getId()]=$a->getObjetivo()->getNombre();
        }  
        
        if(!empty($data) and $data['proyecto']!='t'):
            foreach ($filtroPorProyecto as $a) {
                $arrayFiltro['objetivo'][$a->getObjetivo()->getId()]=$a->getObjetivo()->getNombre();
            } 
        endif;
 
        
        
        return $arrayFiltro;
    }
    /**
     * Displays a form to create a new Prioridadmeta entity.
     *
     */
    public function newAction(Request $request,$idmeta)
    {
        $this->seguridad();
        
        //definiciones
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getRepository('PrincipalBundle:Metas')->find($idmeta);
        $miembroEspacio = $meta->getMiembroespacio();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($miembroEspacio->getUsuario());
        $data=$request->request->all();
        //fin
        
        //consultas
        $actividad=$this->queryActividad($data,$miembroEspacio);
        
        $ticket= $em->getRepository('TicketBundle:ProcesarTicket')->findByResponsable($miembroEspacio);
        
        //busco las tarjetas de una meta en especifico
        $dql = "select x from PrincipalBundle:MetaActividad x join x.actividad a where a.ubicacionPriometa=2 and x.meta= :meta order by a.ordenPriometa ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('meta',$meta);
        $priometa = $query->getResult();
        
        $arrayFiltro=  $this->filtro($data,$miembroEspacio);
        //fin consultas
        
    
        return $this->render('PrincipalBundle:Prioridadmeta:new.html.twig', array(
            'meta' => $meta,
            'ticket'=>$ticket,
            'actividad'=>$actividad,
            'priometa'=>$priometa,
            'arrayFiltro'=>$arrayFiltro,
            'data'=>$data,
            'miembroEspacio'=>$miembroEspacio

        ));
    }

    /**
     * Finds and displays a Prioridadmeta entity.
     *
     */
    public function showAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Prioridadmeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prioridadmeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getUsuario());

        return $this->render('PrincipalBundle:Prioridadmeta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Prioridadmeta entity.
     *
     */
    public function editAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Prioridadmeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prioridadmeta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        
        
        return $this->render('PrincipalBundle:Prioridadmeta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Prioridadmeta entity.
    *
    * @param Prioridadmeta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Prioridadmeta $entity)
    {
        $form = $this->createForm(new PrioridadmetaType(), $entity, array(
            'action' => $this->generateUrl('miembroespacio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Prioridadmeta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->seguridad();
        
        $roles=null;
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Prioridadmeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prioridadmeta entity.');
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
        return $this->render('PrincipalBundle:Prioridadmeta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Prioridadmeta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PrincipalBundle:Prioridadmeta')->find($id);
            
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
                throw $this->createNotFoundException('Unable to find Prioridadmeta entity.');
            }

            
            $em->remove($entity);
            $em->flush();
        }

        $funcionModelo = $this->get('funcionModelo');
        $funcionModelo->gestionarProgis($entity->getUsuario());
        
        return $this->redirect($this->generateUrl('miembroespacio'));
    }

    /**
     * Creates a form to delete a Prioridadmeta entity by id.
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
            ->add('submit', 'submit', array('label' => 'BORARR'))
            ->getForm()
        ;
    }
}
