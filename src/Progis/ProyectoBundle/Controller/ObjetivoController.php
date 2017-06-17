<?php

namespace Progis\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\ProyectoBundle\Entity\Objetivo;
use Progis\ProyectoBundle\Form\ObjetivoType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Objetivo controller.
 *
 */
class ObjetivoController extends Controller
{
    
    public function seguridad($proyecto){
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        if(
               isset($rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]) and (
               $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_CONSULTA']==true  
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_TECNICO']==true  
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_SUPERVISOR']==true)
                
            //coloco el isset porque puede que le hayan colocado rol admin general y no necesariamente se le asigno el rol al proyecto directamente    
            or (isset($rolProyecto[$proyecto->getId()]) and ($rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true
            or $rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_CONSULTA']==true
            or $rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_TECNICO']==true))
                
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }
    
    public function seguridadNuevoEditDel($proyecto){
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');
        $rolProyecto=$session->get('rolProyecto');
        
        if(
               $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_ADMIN']==true
            or $rolGeneralNivel[$proyecto->getNivelorganizacional()->getId()]['ROLE_PROGIS_PROYECTO_TECNICO']==true  
                
            //coloco el isset porque puede que le hayan colocado rol admin general y no necesariamente se le asigno el rol al proyecto directamente    
            or (!empty($rolProyecto) and isset($rolProyecto[$proyecto->getId()]) and ($rolProyecto[$proyecto->getId()]['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true))
                
            or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
    }

    public function totalactividad() {
        $em = $this->getDoctrine()->getManager();
        
        //cuento las objetivos del proyecto
        $dql = "select x from ProyectoBundle:ProcesarActividad x";
        $query = $em->createQuery($dql);
        $actividades = $query->getResult();
        $totalact=array();
        
        //inicializo
        foreach ($actividades as $v) {
            $totalact[$v->getObjetivo()->getId()][$v->getUbicacion()]=0;
        }  
        //sumo
        foreach ($actividades as $v) {
            $totalact[$v->getObjetivo()->getId()][$v->getUbicacion()]=$totalact[$v->getObjetivo()->getId()][$v->getUbicacion()]+1;
        } 
        
        return $totalact;
        
    }
    
    public function comentarioProcesaractividadAction($idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
    
        $dql = "select x from PrincipalBundle:ComentarioProcesarActividad x join x.procesaractividad pa join pa.objetivo o join x.comentarioarchivo ca where o.proyecto = :proyecto order by pa.descripcion ASC, ca.fechahora DESC";
        $query = $em->createQuery($dql);
        $query->setParameter('proyecto',$proyecto);
        $entities = $query->getResult();        
        
        return $this->render('ProyectoBundle:Objetivo:comentarioProcesaractividad.html.twig', array(
            'entity' => $entities,
            'proyecto'=>$proyecto,
        ));
        
    }

    
    
    
    /**********************************ACTIONS****************************/
    /**
     * Lists all Objetivo entities.
     *
     */
    public function indexAction($idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto); 
        $this->seguridad($proyecto);
        
        //listado de objetivos
        $dql = "select x from ProyectoBundle:Objetivo x where x.proyecto = :idproyecto order by x.estatus, x.porcentaje,x.fechainicioestimada,x.fechafinestimada asc";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $entities = $query->getResult();

        //aca identifico las actividades del objetivo para poder mostrarlas
        $actividades=array();
        foreach ($entities as $o) {
            $act = $em->getRepository('ProyectoBundle:ProcesarActividad')->findByObjetivo($o->getId());
            
            if(!empty($act)){
                foreach ($act as $a){
                    $actividades[]=$a->getId();
                }
            }
        }

        
        $dql = "select count(x) from PrincipalBundle:ComentarioProcesarActividad x join x.procesaractividad pa join pa.objetivo o join x.comentarioarchivo ca where o.proyecto = :proyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('proyecto',$proyecto);
        $cantidadComar = $query->getResult(); 

        //cantidad de comentarios en actividades de los objetivos
        $objetivoModelo = $this->get('objetivoModelo');
        $cantidadComentarioObjetivo=$objetivoModelo->cantidadComentarioObjetivos($entities);
        
        

        return $this->render('ProyectoBundle:Objetivo:index.html.twig', array(
            'entities' => $entities,
            'proyecto' => $proyecto,
            'totalact'=>$this->totalactividad(),
            'cantidadComar'=>$cantidadComar
        ));
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Objetivo')->find($id);
        $proyecto = $entity->getProyecto();

        $this->seguridad($proyecto);
        
        $proyectoModelo = $this->get('proyectoModelo');
        $proyectoModelo->porcentajeproyecto($entity->getProyecto()->getId());
        $proyectoModelo->estatusproyecto($entity->getProyecto()->getId());
        
        
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('alert', 'Este objetivo fue eliminado.');
            return $this->redirect($this->generateUrl('progis_principal_homepage'));
        }
        $deleteForm = $this->createDeleteForm($id,$entity->getProyecto()->getId());

        return $this->render('ProyectoBundle:Objetivo:show.html.twig', array(
            'entity'      => $entity,
            'proyecto'      => $proyecto,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function newAction($idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $nivel=$proyecto->getNivelorganizacional();
        
        $this->seguridadNuevoEditDel($proyecto);
        
        $entity = new Objetivo();
        $form   = $this->createCreateForm($entity,$idproyecto);

        return $this->render('ProyectoBundle:Objetivo:new.html.twig', array(
            'entity' => $entity,
            'proyecto' => $proyecto,
            'form'   => $form->createView(),
        ));
    }
    
    private function createCreateForm(Objetivo $entity,$idproyecto)
    {
        $form = $this->createForm(new ObjetivoType(), $entity, array(
            'action' => $this->generateUrl('objetivo_create',array('idproyecto'=>$idproyecto)),
            'method' => 'POST',
        ));
        return $form;
    }
    
    public function generalAction($idproyecto)
    {

        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);       
        $entities = $em->getRepository('ProyectoBundle:Objetivo')->findByProyecto($idproyecto);
        
        return $this->render('ProyectoBundle:Objetivo:general.html.twig', array(
            'entities' => $entities,
            'proyecto' => $proyecto,
            'totalact'=>$this->totalactividad()
        ));
    }
    /**
     * Creates a new Objetivo entity.
     *
     */
    public function createAction(Request $request,$idproyecto)
    {
        $em = $this->getDoctrine()->getManager();
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        
        $this->seguridadNuevoEditDel($proyecto);
        
        
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $entity = new Objetivo();
        $form = $this->createCreateForm($entity,$idproyecto);
        $form->handleRequest($request);
        if ($form->isValid()) {

            //obtengo las fechas estimadas para guardarlas como las reales, cabe destacar que es para que el campo no este vacio ya que las reales varian
            $datos=$form->getData();
            $fechainicioestimada=$datos->getFechainicioestimada();
            $fechafinestimada=$datos->getFechafinestimada();
            //fin
            
            $em = $this->getDoctrine()->getManager();
            $entity->setPorcentaje(0);
            $entity->setEstatus(1);
            $entity->setProyecto($proyecto);
            $entity->setFechainicioreal($fechainicioestimada);
            $entity->setFechafinreal($fechafinestimada);
            $entity->setFechacreacion($fa);
            $em->persist($entity);
            //$em->flush();

            $proyectoModelo = $this->get('proyectoModelo');
            $proyectoModelo->estatusproyecto($idproyecto);
            $proyectoModelo->porcentajeproyecto($idproyecto);
            $proyectoModelo->fechaInicioProyecto($idproyecto);
            $proyectoModelo->fechaFinProyecto($idproyecto);
           
            $this->get('session')->getFlashBag()->add('notice', 'Se ha creado el objetivo exitosamente.');
            return $this->redirect($this->generateUrl('objetivo_show', array('id' => $entity->getId())));
        }

        return $this->render('ProyectoBundle:Objetivo:new.html.twig', array(
            'entity' => $entity,
            'proyecto' => $proyecto,
            'form'   => $form->createView(),
        ));
    }


    public function editAction($id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $entity = $em->getRepository('ProyectoBundle:Objetivo')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());
        $nivel=$proyecto->getNivelorganizacional();
        
        $this->seguridadNuevoEditDel($proyecto);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Objetivo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id,$entity->getProyecto()->getId());

        return $this->render('ProyectoBundle:Objetivo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'proyecto'=>$proyecto
        ));
    }

    /**
    * Creates a form to edit a Objetivo entity.
    *
    * @param Objetivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Objetivo $entity)
    {
        $form = $this->createForm(new ObjetivoType(), $entity, array(
            'action' => $this->generateUrl('objetivo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Objetivo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Objetivo')->find($id);
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($entity->getProyecto()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Objetivo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            //obtengo las fechas estimadas para guardarlas como las reales, cabe destacar que es para que el campo no este vacio ya que las reales varian
            $datos=$editForm->getData();
            $fechainicioestimada=$datos->getFechainicioestimada();
            $fechafinestimada=$datos->getFechafinestimada();
            //fin
            
            $entity->setFechainicioreal($fechainicioestimada);
            $entity->setFechafinreal($fechafinestimada);
            
            $em->flush();

            $proyectoModelo = $this->get('proyectoModelo');
            $proyectoModelo->fechaInicioProyecto($entity->getProyecto()->getId());
            $proyectoModelo->fechaFinProyecto($entity->getProyecto()->getId());
            
            $this->get('session')->getFlashBag()->add('notice', 'El objetivo "'.$entity->getNombre().'" se ha editado exitosamente.');
            return $this->redirect($this->generateUrl('objetivo_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Objetivo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'proyecto'=>$proyecto
        ));
    }
    /**
     * Deletes a Objetivo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyectoBundle:Objetivo')->find($id);
        $idproyecto=$entity->getProyecto()->getId();
        
        $this->seguridadNuevoEditDel($entity->getProyecto());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Objetivo entity.');
        }
        

        $form = $this->createDeleteForm($id,$entity->getProyecto()->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $actividades=$em->getRepository('ProyectoBundle:ProcesarActividad')->findByObjetivo($id);

            if(!empty($actividades)){
                $this->get('session')->getFlashBag()->add('alert', 'No puede borrar la objetivo porque tiene actividades asignadas.');
            }else{

                $em->remove($entity);
                $em->flush();
                
                $proyectoModelo = $this->get('proyectoModelo');
                $proyectoModelo->estatusproyecto($idproyecto);
                $proyectoModelo->porcentajeproyecto($idproyecto);
                $proyectoModelo->fechaInicioProyecto($entity->getProyecto()->getId());
                $proyectoModelo->fechaFinProyecto($entity->getProyecto()->getId());
            }
        }
        
            
        $this->get('session')->getFlashBag()->add('notice', 'El objetivo "'.$entity->getNombre().'" se ha borrado exitosamente.');
        return $this->redirect($this->generateUrl('objetivo',array('idproyecto' => $entity->getProyecto()->getId())));
    }

    /**
     * Creates a form to delete a Objetivo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('objetivo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}


