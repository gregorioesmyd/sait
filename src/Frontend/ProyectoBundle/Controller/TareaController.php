<?php

namespace Frontend\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProyectoBundle\Entity\Tarea;
use Frontend\ProyectoBundle\Form\TareaType;

/**
 * Tarea controller.
 *
 */
class TareaController extends Controller
{

    public function totalactividad() {
        $em = $this->getDoctrine()->getManager();
        
        //cuento las tareas del proyecto
        $dql = "select x from ProyectoBundle:Actividad x";
        $query = $em->createQuery($dql);
        $actividades = $query->getResult();
        $totalact=array();
        
        //inicializo
        foreach ($actividades as $v) {
            $totalact[$v->getTarea()->getId()][$v->getUbicacion()]=0;
        }  
        //sumo
        foreach ($actividades as $v) {
            $totalact[$v->getTarea()->getId()][$v->getUbicacion()]=$totalact[$v->getTarea()->getId()][$v->getUbicacion()]+1;
        } 
        
        return $totalact;
        
    }
    
    public function fechainicioproyecto($idproyecto){

        $em = $this->getDoctrine()->getManager();
        
        //busco la fecha mas baja de las tareas para que sea la fecha de inicio del proyecto
        $dql = "select x from ProyectoBundle:Tarea x where x.proyecto= :idproyecto order by x.fechainicio ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $query->setMaxResults(1);
        $tarea = $query->getResult();
        
        if(!empty($tarea))
            $fechainicio=$tarea[0]->getFechainicio();
        else $fechainicio=null;

        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Proyecto x set x.fechainicio= :fechainicio WHERE x.id = :idproyecto');
        $query->setParameter('fechainicio', $fechainicio);
        $query->setParameter('idproyecto', $idproyecto);
        $query->execute();
    }
    
    //se llama al crear o borrar una actividad
    public function fechafinproyecto($idproyecto){

        $em = $this->getDoctrine()->getManager();
        
        //busco la fecha mas baja de las tareas para que sea la fecha de inicio del proyecto
        $dql = "select max(x.fechafinestimada) from ProyectoBundle:Tarea x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $tarea = $query->getResult();
        
        if(!empty($tarea))
            $fechafin=$tarea[0][1];
        else $fechafin=null;

        //actualizo campos en proyecto
        $query = $em->createQuery('update ProyectoBundle:Proyecto x set x.fechafin= :ffe WHERE x.id = :idproyecto');
        $query->setParameter('ffe', $fechafin);
        $query->setParameter('idproyecto', $idproyecto);
        $query->execute();

    }
    

    public function listadocumentosAction($idtarea)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "select x from ProyectoBundle:Documentoactividad x join x.actividadId a join a.tarea t where t.id= :idtarea";
        $query = $em->createQuery($dql);
        $query->setParameter('idtarea',$idtarea);
        $docuact = $query->getResult();
        
        $tarea = $em->getRepository('ProyectoBundle:Tarea')->find($idtarea); 
        

        return $this->render('ProyectoBundle:Tarea:documentoactividad.html.twig', array(
            'entities' => $docuact,
            'tarea'=>$tarea
        ));
        
    }

    /**
     * Lists all Tarea entities.
     *
     */
    public function indexAction($idproyecto)
    {

        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);  
        $responsable=$proyecto->getResponsable();
        if(!isset($responsable[0])){
            $this->get('session')->getFlashBag()->add('alert', 'Debe agregar un responsable al proyecto.');
            return $this->redirect($this->generateUrl('proyecto_edit', array('id' => $idproyecto)));
        }
        
        $entities = $em->getRepository('ProyectoBundle:Tarea')->findByProyecto($idproyecto);
        
        $proyectoModelo = $this->get('proyectoModelo');
        
        $proyectoModelo->porcentajeproyecto($idproyecto);
        $proyectoModelo->estatusproyecto($idproyecto);
        
        if(!empty($entities)){
        
            require('libs/gantti-master/lib/gantti.php'); 
        
            $data=array();
            foreach ($entities as $v) {

                $fin = strtotime($v->getFechafinestimada()->format("Y-m-d")); //Formato es un campo tipo DATE formato Y-m-d
                $actual = strtotime(\date("Y-m-d")); //Formato es un campo tipo DATE formato Y-m-d



                if($actual>$fin and $v->getPorcentaje()<100)$class='urgent'; 
                else if($v->getPorcentaje()<100)$class='important'; else $class=null;

                $data[]=array(
                            'label' => $v->getNombre(),
                            'start' => $v->getFechaInicio()->format("Y-m-d"), 
                            'end'   => $v->getFechafinestimada()->format("Y-m-d"),
                            'class' => $class
                          );
            }

            $gantti = new \Gantti($data, array(
              'title'      => $proyecto->getNombre(),
              'cellwidth'  => 25,
              'cellheight' => 35,
              'today'      => true
            ));
        } else $gantti=null;

        return $this->render('ProyectoBundle:Tarea:index.html.twig', array(
            'entities' => $entities,
            'proyecto' => $proyecto,
            'totalact'=>$this->totalactividad(),
            'gantti'=>$gantti
        ));
    }
    
    public function generalAction($idproyecto)
    {

        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);       
        $entities = $em->getRepository('ProyectoBundle:Tarea')->findByProyecto($idproyecto);
        
        return $this->render('ProyectoBundle:Tarea:general.html.twig', array(
            'entities' => $entities,
            'proyecto' => $proyecto,
            'totalact'=>$this->totalactividad()
        ));
    }
    /**
     * Creates a new Tarea entity.
     *
     */
    public function createAction(Request $request,$idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        
        $entity = new Tarea();
        $form = $this->createCreateForm($entity,$idproyecto);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPorcentaje(0);
            $entity->setEstatus(1);
            $entity->setProyecto($proyecto);
            $em->persist($entity);
            $em->flush();

            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
            
            $responsable=$entity->getProyecto()->getResponsable();
            foreach ($responsable as $v) {
                $arrayresponsable[]=$v->getUser()->getUsername()."@telesurtv.net";
            }
            
            $message = \Swift_Message::newInstance()   
            ->setSubject('Proyectos-Tareas')  
            ->setFrom($perfil->getNivelorganizacional()->getCorreo())     // we configure the sender
            ->setTo($arrayresponsable)   
            ->setBody( $this->renderView(
                    'ProyectoBundle:Correo:tarea.html.twig',
                    array('tarea' => $entity)
                ), 'text/html');

            $this->get('mailer')->send($message);   
            
            //actualizo fecha al crear o editar
            $this->fechainicioproyecto($idproyecto);
            $this->fechafinproyecto($idproyecto); 
            
            return $this->redirect($this->generateUrl('tarea_show', array('id' => $entity->getId())));
        }

        return $this->render('ProyectoBundle:Tarea:new.html.twig', array(
            'entity' => $entity,
            'proyecto' => $proyecto,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tarea entity.
     *
     * @param Tarea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tarea $entity,$idproyecto)
    {
        $form = $this->createForm(new TareaType(), $entity, array(
            'action' => $this->generateUrl('tarea_create',array('idproyecto'=>$idproyecto)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tarea entity.
     *
     */
    public function newAction($idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        
        $entity = new Tarea();
        $form   = $this->createCreateForm($entity,$idproyecto);

        return $this->render('ProyectoBundle:Tarea:new.html.twig', array(
            'entity' => $entity,
            'proyecto' => $proyecto,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Tarea entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Tarea')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());

        $proyectoModelo = $this->get('proyectoModelo');
        $proyectoModelo->porcentajeproyecto($entity->getProyecto()->getId());
        $proyectoModelo->estatusproyecto($entity->getProyecto()->getId());
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarea entity.');
        }
        $deleteForm = $this->createDeleteForm($id,$entity->getProyecto()->getId());

        return $this->render('ProyectoBundle:Tarea:show.html.twig', array(
            'entity'      => $entity,
            'proyecto'      => $proyecto,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function showgeneralAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Tarea')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarea entity.');
        }
        return $this->render('ProyectoBundle:Tarea:showgeneral.html.twig', array(
            'entity'      => $entity,
            'proyecto'      => $proyecto,
        ));
    }

    /**
     * Displays a form to edit an existing Tarea entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Tarea')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarea entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id,$entity->getProyecto()->getId());

        return $this->render('ProyectoBundle:Tarea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'proyecto'=>$proyecto
        ));
    }

    /**
    * Creates a form to edit a Tarea entity.
    *
    * @param Tarea $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tarea $entity)
    {
        $form = $this->createForm(new TareaType(), $entity, array(
            'action' => $this->generateUrl('tarea_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tarea entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Tarea')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->fechainicioproyecto($entity->getProyecto()->getId());
            $this->fechafinproyecto($entity->getProyecto()->getId()); 
            return $this->redirect($this->generateUrl('tarea_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Tarea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'proyecto'=>$proyecto
        ));
    }
    /**
     * Deletes a Tarea entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Tarea')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarea entity.');
        }
        

        $form = $this->createDeleteForm($id,$entity->getProyecto()->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $actividades=$em->getRepository('ProyectoBundle:Actividad')->findByTarea($id);

            if(!empty($actividades)){
                $this->get('session')->getFlashBag()->add('alert', 'No puede borrar la tarea porque tiene actividades asignadas.');
            }else{

                $em->remove($entity);
                $em->flush();

                $this->fechainicioproyecto($entity->getProyecto()->getId());
                $this->fechafinproyecto($entity->getProyecto()->getId());
            }
            
        }

        return $this->redirect($this->generateUrl('tarea',array('idproyecto' => $entity->getProyecto()->getId())));
    }

    /**
     * Creates a form to delete a Tarea entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tarea_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}

