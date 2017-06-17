<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\PrincipalBundle\Entity\Metas;
use Progis\PrincipalBundle\Form\MetasType;

/**
 * Metas controller.
 *
 */
class MetasController extends Controller
{
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }

    public function queryActividad($data,$miembroEspacio,$meta){
        $em = $this->getDoctrine()->getManager();
        
        //si no selecciono nada
        if(empty($data) or (!empty($data) and $data['proyecto']=='t') and $data['objetivo']=='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.responsable mp join x.objetivo o
                    where mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and x.ubicacion!=4 and x.ubicacion!=3 and
                    (
                        (o.fechainicioestimada<= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta) or
                        (o.fechainicioestimada>= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta)
                    )
                    order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('miembroespacio',$miembroEspacio);
            $query->setParameter('fechahastaMeta',$meta->getFechahasta());
            $query->setParameter('fechadesdeMeta',$meta->getFechadesde());
            
        //si selecciono un proyecto
        elseif($data['proyecto']!='t' and $data['objetivo']=='t'):    
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p join x.responsable mp  
                    where p.id= :idp and mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and x.ubicacion!=4 and x.ubicacion!=3 and
                    (
                        (o.fechainicioestimada<= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta) or
                        (o.fechainicioestimada>= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta)
                    ) 
                    order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('miembroespacio',$miembroEspacio);
            $query->setParameter('idp',$data['proyecto']); 
            $query->setParameter('fechahastaMeta',$meta->getFechahasta());
            $query->setParameter('fechadesdeMeta',$meta->getFechadesde());
            
            
        //si selecciono un proyecto y un objetivo
        elseif($data['proyecto']!='t' and $data['objetivo']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p join x.responsable mp where o.id= :ido and p.id= :idp and mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and x.ubicacion!=4 and x.ubicacion!=3 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('miembroespacio',$miembroEspacio);
            $query->setParameter('idp',$data['proyecto']); 
            $query->setParameter('ido',$data['objetivo']); 
            
        //si lecciono solo un objetivo
        elseif($data['proyecto']=='t' and $data['objetivo']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p join x.responsable mp where o.id= :ido and mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and x.ubicacion!=4 and x.ubicacion!=3 order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('miembroespacio',$miembroEspacio);
            $query->setParameter('ido',$data['objetivo']); 
        endif;
        

        $actividad = $query->getResult();
        return $actividad;
    }
    
    public function filtro($data,$miembroEspacio,$meta){
        $arrayFiltro['proyecto']=array();
        $arrayFiltro['objetivo']=array();

        $em = $this->getDoctrine()->getManager();
        
        //si no ha filtrado nada
        $dql = "select x from ProyectoBundle:ProcesarActividad x join x.responsable mp join x.objetivo o join o.proyecto p 
                where 
                mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and
                (
                    (o.fechainicioestimada<= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta) or
                    (o.fechainicioestimada>= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta)
                )
                
                order by x.fechacreacion ASC";
        
        $query = $em->createQuery($dql);
        $query->setParameter('miembroespacio',$miembroEspacio);
        $query->setParameter('fechahastaMeta',$meta->getFechahasta());
        $query->setParameter('fechadesdeMeta',$meta->getFechadesde());
        $general = $query->getResult();
        
        
        //si filtra solo proyecto
        if(!empty($data) and $data['proyecto']!='t'):
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p join x.responsable mp 
                    where p.id=:idp and mp.miembroespacio= :miembroespacio and x.ubicacionPriometa=1 and
                    (
                        (o.fechainicioestimada<= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta) or
                        (o.fechainicioestimada>= :fechadesdeMeta and o.fechafinestimada>= :fechahastaMeta)
                    )
                    order by x.fechacreacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('miembroespacio',$miembroEspacio);
            $query->setParameter('fechahastaMeta',$meta->getFechahasta());
            $query->setParameter('fechadesdeMeta',$meta->getFechadesde());
            $query->setParameter('idp',$data['proyecto']);  
            $filtroPorProyecto = $query->getResult();
        endif;

        
        //
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

    function diasEntreFechas($inicio, $fin)
    {

        $fecha1 = new \DateTime($inicio);
        $fecha2 = new \DateTime($fin);
        $fecha = $fecha1->diff($fecha2);
        //printf('%d años, %d meses, %d días, %d horas, %d minutos', $fecha->y, $fecha->m, $fecha->d, $fecha->h, $fecha->i);
        
        return $fecha->d;
    }


    public function priometaAction(Request $request,$idmeta)
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
        $actividad=$this->queryActividad($data,$miembroEspacio,$meta);
        $ticket= $em->getRepository('TicketBundle:ProcesarTicket')->findByResponsable($miembroEspacio);
        
        //busco las tarjetas ubicadas como metas
        $dql = "select x from PrincipalBundle:MetaActividad x join x.actividad a where a.ubicacionPriometa=2 and x.meta= :meta order by a.ordenPriometa ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('meta',$meta);
        $priometa = $query->getResult();
        
        $arrayFiltro=  $this->filtro($data,$miembroEspacio,$meta);
        //fin consultas
    
        return $this->render('PrincipalBundle:Metas:priometa.html.twig', array(
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
     * Lists all Metas entities.
     *
     */
    public function indexAction($id)
    {
        $this->seguridad();

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PrincipalBundle:Metas')->findByMiembroespacio($id);
        $miembroEspacio = $em->getRepository('PrincipalBundle:MiembroEspacio')->find($id);

        $metaModelo=  $this->get("metaModelo");
        $estatusMetas=$metaModelo->estatus($entities);
        
        
        return $this->render('PrincipalBundle:Metas:index.html.twig', array(
            'entities' => $entities,
            'miembroEspacio'=> $miembroEspacio,
            
        ));
    }
    /**
     * Creates a new Metas entity.
     *
     */
    public function createAction(Request $request,$id)
    {
        $this->seguridad();
        
        $entity = new Metas();
        $form = $this->createCreateForm($entity,$id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $miembroEspacio = $em->getRepository('PrincipalBundle:MiembroEspacio')->find($id);
        
        if ($form->isValid()) {
            $data=$form->getData();
            $fa=\date("Y-m-d");

            //valido que la fecha desde no sea menor a la actual
            if($data->getFechadesde()->format("Y-m-d")<$fa):
                $this->get('session')->getFlashBag()->add('alert', 'La fecha desde no puede ser menor a la fecha actual.');
                return $this->render('PrincipalBundle:Metas:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                    'miembroEspacio'=> $miembroEspacio
                ));
            endif;

            //calculo la cantidad de horas respecto a la fecha
            $dias=$this->diasEntreFechas($data->getFechadesde()->format("Y-m-d"), $data->getFechahasta()->format("Y-m-d"));
            $arrayDiasJornada=explode("-",$miembroEspacio->getJornadalaboral()->getDias());
            $cantHoraDiaria=$miembroEspacio->getJornadalaboral()->getHorasDiarias();
            
            $contDiasEnJornada=0;
            for($i=0;$i<=$dias;$i++){
                //voy avanzando desde la fecha de inicio a la de fin para saber que dia de la semana es y si esta en su jornada laboral
                $nuevafecha = strtotime ( '+'.$i.' day' , strtotime ( $data->getFechadesde()->format("Y-m-d") ) ) ;
                $diaSemana=date("N",$nuevafecha);
                if (in_array($diaSemana, $arrayDiasJornada)) {
                    $contDiasEnJornada++;
                }
            }
            
            $totalHoras=$cantHoraDiaria*$contDiasEnJornada;

            $entity->setHoras($totalHoras);
            $entity->setJornadalaboral($miembroEspacio->getJornadalaboral());
            
            $estatus = $em->getRepository('PrincipalBundle:Estatus')->find(1);
            $entity->setEstatus($estatus);
            $entity->setPorcentaje(0);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('metas_show', array('id' => $entity->getId())));
        }

        return $this->render('PrincipalBundle:Metas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'miembroEspacio'=> $miembroEspacio
        ));
    }

    /**
     * Creates a form to create a Metas entity.
     *
     * @param Metas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Metas $entity,$id)
    {
        $this->seguridad();
        
        $form = $this->createForm(new MetasType(), $entity, array(
            'action' => $this->generateUrl('metas_create',array('id'=>$id)),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Metas entity.
     *
     */
    public function newAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $entity = new Metas();
        $form   = $this->createCreateForm($entity,$id);
        
        $miembroEspacio = $em->getRepository('PrincipalBundle:MiembroEspacio')->find($id);

        return $this->render('PrincipalBundle:Metas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'miembroEspacio'=> $miembroEspacio
        ));
    }

    /**
     * Finds and displays a Metas entity.
     *
     */
    public function showAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Metas')->find($id);
        $miembroEspacio=$entity->getMiembroespacio();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PrincipalBundle:Metas:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'miembroEspacio'=> $miembroEspacio
        ));
    }

    /**
     * Displays a form to edit an existing Metas entity.
     *
     */
    public function editAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Metas')->find($id);
        $miembroEspacio=$entity->getMiembroespacio();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metas entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PrincipalBundle:Metas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'miembroEspacio'=> $miembroEspacio
        ));
    }

    /**
    * Creates a form to edit a Metas entity.
    *
    * @param Metas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Metas $entity)
    {
        $form = $this->createForm(new MetasType(), $entity, array(
            'action' => $this->generateUrl('metas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Metas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('PrincipalBundle:Metas')->find($id);
        $miembroEspacio=$entity->getMiembroespacio();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data=$editForm->getData();
            $fa=\date("Y-m-d");

            //valido si cambiaron las fechas y que la fecha desde no sea menor a la actual
            /*if($entity->getFechadesde()->format("Y-m-d")!=$data->getFechadesde()->format("Y-m-d") and $entity->getFechahasta()->format("Y-m-d")!=$data->getFechahasta()->format("Y-m-d") and $data->getFechadesde()->format("Y-m-d")<$fa):
                $this->get('session')->getFlashBag()->add('alert', 'La fecha desde no puede ser menor a la fecha actual.');
                return $this->render('PrincipalBundle:Metas:edit.html.twig', array(
                    'entity'      => $entity,
                    'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'miembroEspacio'=> $miembroEspacio
                ));
            endif;*/

            //calculo la cantidad de horas respecto a la fecha
            $dias=$this->diasEntreFechas($data->getFechadesde()->format("Y-m-d"), $data->getFechahasta()->format("Y-m-d"));
            $arrayDiasJornada=explode("-",$data->getJornadalaboral()->getDias());
            $cantHoraDiaria=$miembroEspacio->getJornadalaboral()->getHorasDiarias();
            
            $contDiasEnJornada=0;
            for($i=0;$i<=$dias;$i++){
                //voy avanzando desde la fecha de inicio a la de fin para saber que dia de la semana es y si esta en su jornada laboral
                $nuevafecha = strtotime ( '+'.$i.' day' , strtotime ( $data->getFechadesde()->format("Y-m-d") ) ) ;
                $diaSemana=date("N",$nuevafecha);
                if (in_array($diaSemana, $arrayDiasJornada)) {
                    $contDiasEnJornada++;
                }
            }
            
            $totalHoras=$cantHoraDiaria*$contDiasEnJornada;

            $entity->setHoras($totalHoras);
            
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Meta editada con exito.');
            return $this->redirect($this->generateUrl('metas_show', array('id' => $id)));
        }

        return $this->render('PrincipalBundle:Metas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'miembroEspacio'=> $miembroEspacio
        ));
    }
    /**
     * Deletes a Metas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {

        $this->seguridad();
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PrincipalBundle:Metas')->find($id);
            $miembroEspacio=$entity->getMiembroespacio();
            $entity = $em->getRepository('PrincipalBundle:Metas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Metas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('notice', 'Meta eliminada con exito.');
        return $this->redirect($this->generateUrl('metas',array('id'=>$miembroEspacio->getId())));
    }

    /**
     * Creates a form to delete a Metas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('metas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
