<?php

namespace Frontend\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProyectoBundle\Entity\Actividad;
use Frontend\ProyectoBundle\Form\ActividadType;

use Administracion\UsuarioBundle\Resources\Misclases\Funcion;

/**
 * Actividad controller.
 *
 */
class ActividadController extends Controller
{

   public function retardoactividad($idactividad){
        $em = $this->getDoctrine()->getManager();
        $act = $em->getRepository('ProyectoBundle:Actividad')->find($idactividad);
        $fa=new \DateTime(\date("d-m-Y G:i:s"));

        //calculo el tiempo estimado de la actividad 
        if($act->getTipotiempo()==1)$tt='day';
        else if($act->getTipotiempo()==2)$tt='hour';
        else if($act->getTipotiempo()==3)$tt='minute';
        $duracionestimada = strtotime ( '+'.$act->getTiempoestimado().' '.$tt , strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;
        $duracionestimada = date ( 'Y-m-d G:i:s' , $duracionestimada );
        $duracionestimada=new \DateTime($duracionestimada);
        
        //convierto los datos de tiempo real
        $tiemporeal=$act->getTiemporeal();
        $tiemporeal=  explode("-", $tiemporeal);
        
        //dia a segundo
        $diasegundo=$tiemporeal[0]*86400;
        
        //hora a segundo
        $horasegundo=$tiemporeal[1]*3600;

        //minuto a segundo
        $minutosegundo=$tiemporeal[2]*60;
        
        $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tiemporeal[3];

        $duracionreal = strtotime ( '+'.$segundototal.' second', strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;
        $duracionreal = date ( 'Y-m-d G:i:s' , $duracionreal );
        $duracionreal=new \DateTime($duracionreal);
        
        if(strtotime($duracionreal->format("d-m-Y G:i:s")) >= strtotime($duracionestimada->format("d-m-Y G:i:s"))) return 'true'; else return 'false';
                
    }
    
    //suma el tiempo si la actividad estuvo en pausa o devualta de rendicion
    public function sumartiempo($tanterior,$tnuevo) {
        $fa1=new \DateTime(\date("d-m-Y G:i:s"));
        $fa2=$fa1;
        
          //convierto los datos de tiempo real
        $tanterior=  explode("-", $tanterior);
        $tnuevo=  explode("-", $tnuevo);
        //dia a segundo
        $diasegundo=($tanterior[0]+$tnuevo[0])*86400;
        //hora a segundo
        $horasegundo=($tanterior[1]+$tnuevo[1])*3600;
        //minuto a segundo
        $minutosegundo=($tanterior[2]+$tnuevo[2])*60;
        $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tanterior[3]+$tnuevo[3];
        
        $duracion = strtotime ( '+'.$segundototal.' second', strtotime ( $fa1->format("d-m-Y G:i:s") ) ) ;
        $duracion = date ( 'Y-m-d G:i:s' , $duracion );
        $duracion=new \DateTime($duracion);      
        
        $intervalo=$duracion->diff($fa2);
        
        return $intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s;
    }
    
    public function guardasumatiempo($act) {
        $em = $this->getDoctrine()->getManager();
        
        //calculo fecha fin y tiempo real
        $comienzo=new \DateTime($act->getComienzo()->format("d-m-Y G:i:s"));
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $intervalo=$comienzo->diff($fa);

        //verifico si ya hay un tiempo real guardado y lo sumo
        $tiemporeal=$act->getTiemporeal();
        $tiempo=array();
        if($tiemporeal==null){
            $tiempo=$intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s;
        }else{

            $tiempo=$this->sumartiempo($tiemporeal, $intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s);
        }
        

        //guardo fecha fin y tiempo real
        $query = $em->createQuery('update ProyectoBundle:Actividad x set x.fin= :fin, x.tiemporeal= :tr WHERE x.id = :idactividad');
        $query->setParameter('fin',$fa);
        $query->setParameter('tr',$tiempo);
        $query->setParameter('idactividad', $act->getId());
        $query->execute();  
    }
    
    public function calculacuentaregresiva($e) {
       
        $comienzo=$tiemporeal=$e->getComienzo();
        
        //fecha fin y tiempo real
        $fa1=new \DateTime(\date("d-m-Y G:i:s"));

        //sumo tiempo usado
        $tiemporeal=$e->getTiemporeal();

        //si hay un tiempo guardado porque la actividad ya fue movida
        if($tiemporeal!=null){
            
            //sumo el tiempo de la actividad a la fecha actual y obtengo el estimado
                if($e->getTipotiempo()==1)$tt='day';
                else if($e->getTipotiempo()==2)$tt='hour';
                else if($e->getTipotiempo()==3)$tt='minute';
                $tiempoestimado = strtotime ( '+'.$e->getTiempoestimado().' '.$tt , strtotime ( $comienzo->format("d-m-Y G:i:s") ) ) ;
        
            //sumo el tiempo utilizado a la fecha actual y obtengo el utilizado
                $tiemporeal=  explode("-", $tiemporeal);
                //dia a segundo
                $diasegundo=$tiemporeal[0]*86400;
                //hora a segundo
                $horasegundo=$tiemporeal[1]*3600;
                //minuto a segundo
                $minutosegundo=$tiemporeal[2]*60;
                $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tiemporeal[3];

                $tiempoconsumido = strtotime ( '+'.$segundototal.' second' , strtotime ( $fa1->format("d-m-Y G:i:s") ) ) ;


            //si la actividad ya se ha retrasado coloco 0 en la cuenta regresiva
            if($tiempoconsumido>$tiempoestimado)
                $this->cuentaregresiva[$e->getId()]=0;
            else{

                //si no se ha retrasado convierto ambas fecha para poder hacer un diff
                $tiempoestimado = date ( 'Y-m-d G:i:s' , $tiempoestimado );
                $tiempoestimado=new \DateTime($tiempoestimado);

                $tiempoconsumido = date ( 'Y-m-d G:i:s' , $tiempoconsumido );
                $tiempoconsumido=new \DateTime($tiempoconsumido);
                
                $intervalo=$tiempoestimado->diff($tiempoconsumido);

                $this->cuentaregresiva[$e->getId()]=str_pad($intervalo->d,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->h,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->i,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->s,2,"0",STR_PAD_LEFT);
            }
        } else{
            
                //aca calculo el tiempo transcurrido cuando la actividad no ha sido pausada
                //calculo el tiempo estimado sumando el tiempo de la actividad a la fecha de comienzo
                //luego hago un dif entre el tiempo estimado y la fecha actual
            
                
            
                if($e->getTipotiempo()==1)$tt='day';
                else if($e->getTipotiempo()==2)$tt='hour';
                else if($e->getTipotiempo()==3)$tt='minute';
                $tiempoestimado = strtotime ( '+'.$e->getTiempoestimado().' '.$tt , strtotime ( $comienzo->format("d-m-Y G:i:s") ) ) ;
                
                $tiempoactual=strtotime ( $fa1->format("d-m-Y G:i:s") );
                
                if($tiempoactual>$tiempoestimado)
                    $this->cuentaregresiva[$e->getId()]=0;
                else{
                
                    $tiempoestimado = date ( 'Y-m-d G:i:s' , $tiempoestimado );
                    $tiempoestimado=new \DateTime($tiempoestimado);

                    $intervalo=$tiempoestimado->diff($fa1);

                    $this->cuentaregresiva[$e->getId()]=str_pad($intervalo->d,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->h,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->i,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->s,2,"0",STR_PAD_LEFT);
                }
  
        }
        return $this->cuentaregresiva;
        
    }
    
    /**
     * Lists all Actividad entities.
     *
     */
    
    public function indexAction($idtarea)
    {
        $em = $this->getDoctrine()->getManager();

        $tarea = $em->getRepository('ProyectoBundle:Tarea')->find($idtarea);
        $entities = $em->getRepository('ProyectoBundle:Actividad')->findBy(array('tarea'=>$tarea), array('id' => 'ASC'));
        
        $tareaModelo = $this->get('tareaModelo');
        //actualizo el estatus de las tareas si hay o no actividades
        $tareaModelo->estatustarea($idtarea);
        $tareaModelo->porcentajetarea($idtarea);
        
              
        //cuenta regresiva
        
        $this->cuentaregresiva=null;
        //verifico los tiempos de culminacion
        $duracionactividad=array();
        foreach ($entities as $e) {
            if($e->getUbicacion()==2){
                $this->calculacuentaregresiva($e);
            }
            //muestro el tiempo que lleva consumido
            if($e->getUbicacion()==3 or $e->getUbicacion()==4 or $e->getUbicacion()==5 or ($e->getUbicacion()==1 and $e->getTiemporeal()!=null)){
                
                $r=$this->retardoactividad($e->getId());
                $tiemporeal=explode("-",$e->getTiemporeal());
                if($r=='false'){
                    $duracionactividad[$e->getId()]['tiemposobrante']="D:".$tiemporeal[0]." | H:".$tiemporeal[1]." | M:".$tiemporeal[2]." | S:".$tiemporeal[3];
                }
                else{
                    $duracionactividad[$e->getId()]['tiemporetardo']="D:".$tiemporeal[0]." | H:".$tiemporeal[1]." | M:".$tiemporeal[2]." | S:".$tiemporeal[3];
                }
            }
    
        }

        return $this->render('ProyectoBundle:Actividad:index.html.twig', array(
            'entities' => $entities,
            'tarea'=>$tarea,
            'cuentaregresiva'=>$this->cuentaregresiva,
            'duracionactividad'=>$duracionactividad
        ));
    }
    
    /**
     * Lists all Actividad entities.
     *
     */
    public function generalAction($idtarea)
    {
        $em = $this->getDoctrine()->getManager();

        $tarea = $em->getRepository('ProyectoBundle:Tarea')->find($idtarea);
        $entities = $em->getRepository('ProyectoBundle:Actividad')->findBy(array('tarea'=>$tarea), array('id' => 'ASC'));
        
        return $this->render('ProyectoBundle:Actividad:general.html.twig', array(
            'entities' => $entities,
            'tarea'=>$tarea,
        ));
    }
    /**
     * Creates a new Actividad entity.
     *
     */
    public function createAction(Request $request,$idtarea)
    {
        $em = $this->getDoctrine()->getManager();

        //genero formulario con los integrantes de la unidad
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        $tarea = $em->getRepository('ProyectoBundle:Tarea')->find($idtarea);
        
        $entity = new Actividad();
        $form = $this->createCreateForm($entity,$idtarea,$usuariounidad);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUbicacion(1);
            $entity->setTarea($tarea);
            $entity->setCorreoretardoproceso(false);
            $em->persist($entity);
            $em->flush();

            //si creo una nueva actividad la fecha de fin real la coloco en blanco
            $query = $em->createQuery('update ProyectoBundle:Tarea x set x.fechafinreal=null WHERE x.id = :idtarea');
            $query->setParameter('idtarea', $idtarea);
            $query->execute();  
            
            return $this->redirect($this->generateUrl('actividad_show', array('id' => $entity->getId())));
        }

        return $this->render('ProyectoBundle:Actividad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tarea'=>$tarea,
            'idusuario'=>$idusuario
        ));
    }

    /**
     * Creates a form to create a Actividad entity.
     *
     * @param Actividad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Actividad $entity,$idtarea,$usuariounidad)
    {
        $form = $this->createForm(new ActividadType($usuariounidad), $entity, array(
            'action' => $this->generateUrl('actividad_create',array('idtarea'=>$idtarea)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Actividad entity.
     *
     */
    public function newAction($idtarea)
    {
        $em = $this->getDoctrine()->getManager();
        $tarea = $em->getRepository('ProyectoBundle:Tarea')->find($idtarea);
        
        //genero formulario con los integrantes de la unidad
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        $entity = new Actividad();
        $form   = $this->createCreateForm($entity,$idtarea,$usuariounidad);
        return $this->render('ProyectoBundle:Actividad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tarea'=>$tarea,
            'idusuario'=>$idusuario
        ));
    }

    /**
     * Finds and displays a Actividad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Actividad')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Actividad:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

        public function showgeneralAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Actividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        return $this->render('ProyectoBundle:Actividad:showgeneral.html.twig', array(
            'entity'      => $entity,
        ));
    }
    
    public function ubicacionAction($id,$direccion)
    {
        $em = $this->getDoctrine()->getManager();
        $act = $em->getRepository('ProyectoBundle:Actividad')->find($id);
        $num=$act->getUbicacion();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
     
        
        $fitarea= $act->getTarea()->getFechainicio()->format("d-m-Y");
        if(strtotime($fitarea)>strtotime("now")){
            $this->get('session')->getFlashBag()->add('alert', 'La fecha de inicio de la tarea es mayor a la fecha actual.');
            return $this->redirect($this->generateUrl('actividad', array('idtarea'=>$act->getTarea()->getId()))); 
        }

        
        //va directo a dependencia
        if($direccion=='dep')$num=5;
        //sale de dependencia
        else if($direccion=='nuev')$num=1;
        
        //salculmina
        else if($direccion=='cul')$num=4;
        
        else if($direccion=='der' and $num!=4)$num=$num+1;
        else if($direccion=='izq' and $num!=1) $num=$num-1;
        
        if (!$this->get('security.context')->isGranted('ROLE_PROYECTO_ADMIN')) {
            if($direccion=='der' and ($num==2 or $num==3 ) or $direccion=='izq' and $num==1){
                //valido que el que mueva la tarjeta sea unicamente el responsable
                if($act->getResponsable()->getId()!=$idusuario){
                    $this->get('session')->getFlashBag()->add('alert', 'Usted no es el responsable de esta actividad, por lo tanto no puede moverla.');
                    return $this->redirect($this->generateUrl('actividad', array('idtarea'=>$act->getTarea()->getId()))); 
                }
            }
        }
        
        
       if($num==1){
            //verifico si la actividad viene de proceso y no de dependencia, entonces calculo tiempo y reseteo fechas
            if($act->getUbicacion()!=5){
                $this->guardasumatiempo($act);

                $query = $em->createQuery('update ProyectoBundle:Actividad x set x.comienzo=null,x.fin=null WHERE x.id = :idactividad');
                $query->setParameter('idactividad', $id);
                $query->execute();     
            }
        }
        
       if($num==5){
           
            //verifico si la actividad viene de proceso y calculo tiempos, no de nuevo ni de revision
            if($act->getUbicacion()!=1 and $act->getUbicacion()!=3){
                $this->guardasumatiempo($act);

                $query = $em->createQuery('update ProyectoBundle:Actividad x set x.comienzo=null,x.fin=null WHERE x.id = :idactividad');
                $query->setParameter('idactividad', $id);
                $query->execute();     
            }
        }
        
        //wn proceso
        if($num==2){
            //busco si solo hay en proceso, revision o dependencia
            $dql = "select x from ProyectoBundle:Actividad x where x.responsable= :idresponsable and x.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('idresponsable',$act->getResponsable()->getId());
            $actx = $query->getResult();
            if(!empty($actx)){
              $this->get('session')->getFlashBag()->add('alert', 'Tiene una actividad en curso en la tarea "'.  strtoupper($actx[0]->getTarea()->getNombre()).'" del proyecto "'.strtoupper($actx[0]->getTarea()->getProyecto()->getNombre()).'", ¡debe terminarla!.');
             return $this->redirect($this->generateUrl('actividad', array('idtarea'=>$act->getTarea()->getId())));
            }   
           
            //comienzo el conteo y pongo null en caso de que se haya guardado el fin
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
            $query = $em->createQuery('update ProyectoBundle:Actividad x set x.comienzo= :comienzo, x.fin=null WHERE x.id = :idactividad');
            $query->setParameter('comienzo',$fa);
            $query->setParameter('idactividad', $id);
            $query->execute();  
        }

        //actualizo campos en actividades
        $query = $em->createQuery('update ProyectoBundle:Actividad x set x.ubicacion= :ubicacion WHERE x.id = :idactividad');
        $query->setParameter('ubicacion', $num);
        $query->setParameter('idactividad', $id);
        $query->execute(); 
        
        //en revision
        if($num==3){
            //envio correo de revision
            $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            $responsable=$act->getTarea()->getProyecto()->getResponsable();
            foreach ($responsable as $v) {
                $arrayresponsable[]=$v->getUser()->getUsername()."@telesurtv.net";
            }
            $message = \Swift_Message::newInstance()   
            ->setSubject('Proyectos-Revisión de Actividad')  
            ->setFrom($perfil->getNivelorganizacional()->getCorreo())     // we configure the sender
            ->setTo($arrayresponsable)   
            ->setBody( $this->renderView(
                    'ProyectoBundle:Correo:revisionactividad.html.twig',
                    array('actividad' => $act)
                ), 'text/html');

            $this->get('mailer')->send($message);  
            //fin correo revision
            $this->guardasumatiempo($act);
        }

        else if($num==4){
            $r=$this->retardoactividad($id);
            $query = $em->createQuery('update ProyectoBundle:Actividad x set x.retardo= :retardo WHERE x.id = :idactividad');
            $query->setParameter('retardo',$r);
            $query->setParameter('idactividad', $id);
            $query->execute();  
        }
        

        
        
        /*else if ($num==5){
            //comienzo el conteo y pongo null en caso de que se haya guardado el fin
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
            $query = $em->createQuery('update ProyectoBundle:Actividad x set x.comienzo= :comienzo, x.estatuscomienzo=true WHERE x.estatuscomienzo=false and x.id = :idactividad');
            $query->setParameter('comienzo',$fa);
            $query->setParameter('idactividad', $id);
            $query->execute();  
            
            //comienzo el conteo y pongo null en caso de que se haya guardado el fin
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
            $query = $em->createQuery('update ProyectoBundle:Actividad x set x.fin=null,x.estatusfin=false,x.tiemporeal=null WHERE x.id = :idactividad');
            $query->setParameter('idactividad', $id);
            $query->execute();  
        }*/
        
        
        
        return $this->redirect($this->generateUrl('actividad', array('idtarea' => $act->getTarea()->getId())));
    }
    /**
     * Displays a form to edit an existing Actividad entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        //genero formulario con los integrantes de la unidad
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        $entity = $em->getRepository('ProyectoBundle:Actividad')->find($id);
        
        if($entity->getTiemporeal()!=null){
            $this->get('session')->getFlashBag()->add('alert', 'Estas pillado(a), no le vas a modificar el tiempo a la actividad!!');
            return $this->redirect($this->generateUrl('actividad', array('idtarea'=>$entity->getTarea()->getId()))); 
        }
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $editForm = $this->createEditForm($entity,$usuariounidad);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Actividad:edit.html.twig', array(
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
    private function createEditForm(Actividad $entity,$usuariounidad)
    {
        $form = $this->createForm(new ActividadType($usuariounidad), $entity, array(
            'action' => $this->generateUrl('actividad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Actividad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        //genero formulario con los integrantes de la unidad
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        $entity = $em->getRepository('ProyectoBundle:Actividad')->find($id);
        $ubicacion=$entity->getUbicacion();
        $tarea=$entity->getTarea();
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity,$usuariounidad);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUbicacion($ubicacion);
            $entity->setTarea($tarea);
            $em->flush();
            return $this->redirect($this->generateUrl('actividad_show', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Actividad:edit.html.twig', array(
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
            $entity = $em->getRepository('ProyectoBundle:Actividad')->find($id);
            $idtarea=$entity->getTarea()->getId();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Actividad entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('actividad',array('idtarea'=>$idtarea)));
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
}
