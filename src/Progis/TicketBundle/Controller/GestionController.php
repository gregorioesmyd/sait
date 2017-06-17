<?php

namespace Progis\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Progis\TicketBundle\Entity\ProcesarTicket;
use Progis\TicketBundle\Entity\Reasignado;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GestionController extends Controller
{
    public function seguridad() {

        $admin=false;if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolTicket($session,$admin);
    }
    
    //cuento los tickets por categoria
    public function cantidad($idNivelesUsuarioRolTicket) {
        $em = $this->getDoctrine()->getManager();

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        //tickets asignados
        $dql = "select count(p) from TicketBundle:ProcesarTicket p join p.responsable me where me.usuario= :usuario and p.ubicacion!=4";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $cantasignado= $query->getResult();
        
        
        //otros tickets asignados sin cerrar
        $dql = "select count(p) from TicketBundle:ProcesarTicket p join p.responsable me join p.ticket t where me.usuario!= :usuario and t.nivelorganizacional in (:idnivel) and t.estatus!=4";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $cantotros= $query->getResult();

        $cantidad['cantasignado']=$cantasignado[0][1];
        $cantidad['cantotros']=$cantotros[0][1];

        $dql = "select count(p) from TicketBundle:Ticket p where p.estatus=1 and p.nivelorganizacional in (:idnivel)";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $cantnuev= $query->getResult();

        $dql = "select count(p) from TicketBundle:Ticket p where p.estatus=3 and p.nivelorganizacional in (:idnivel)";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $cantreag= $query->getResult();

        $dql = "select count(p) from TicketBundle:ProcesarTicket p join p.ticket t where t.estatus=2 and t.nivelorganizacional in (:idnivel)";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $cantasig= $query->getResult();

        $dql = "select count(p) from TicketBundle:ProcesarTicket p join p.ticket t where t.estatus=4 and t.nivelorganizacional in (:idnivel)";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $cantacerr= $query->getResult();

        $cantidad['cantnuev']=$cantnuev[0][1];
        $cantidad['cantreag']=$cantreag[0][1];
        $cantidad['cantasig']=$cantasig[0][1];
        $cantidad['cantcerr']=$cantacerr[0][1];


        


        return $cantidad;
    }
    
    //unidades de la direccion
    public function unidadesDireccion(){
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $codigoNivel=substr($perfil->getNivelorganizacional()->getCodigo(),0,-3);
        if($this->get('security.context')->isGranted('ROLE_PROGIS_HELPDESK'))
        $codigoNivel=$perfil->getNivelorganizacional()->getCodigo();

        $dql = "select n from UsuarioBundle:Nivelorganizacional n where n.codigo like :codigo and n.codigo!= :nivelActual";
        $query = $em->createQuery($dql);
        $query->setParameter('codigo',"%".$codigoNivel."__%");
        $query->setParameter('nivelActual',$perfil->getNivelorganizacional()->getCodigo());
        $nivelUnidades = $query->getResult();
        return $nivelUnidades;
    }

    //consulto las categorias de la unidad
    public function catsub($idNivelTicket){
        
        $em = $this->getDoctrine()->getManager();
        $dql = "select s from TicketBundle:Subcategoria s join s.categoria c where c.id=s.categoria and c.nivelorganizacional in (:idnivel) order by c.nivelorganizacional ASC, s.subcategoria asc ";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel', $idNivelTicket);
        $subcat = $query->getResult();
        
        $arraySubcat=array();
        foreach ($subcat as $value) {
            $arraySubcat[$value->getCategoria()->getNivelorganizacional()->getDescripcion()][$value->getCategoria()->getCategoria()][]=$value;
        }
        
        
        return $arraySubcat;
    }
    
    /***************** FUNCIONES ACTION ****************/
    
    public function valorarAction($id,$valor) {
        
        $em = $this->getDoctrine()->getManager();
        $ticket =  $em->getRepository('TicketBundle:Ticket')->find($id);
        $yaValorado=0;
        
        if($ticket->getValoracion()==null):
            $yaValorado=1;
        
            $ticket->setValoracion($valor);
            $em->flush();   
            


            
            //echo "<script>alert('Gracias por valorarnos.');this.close();</script>";
        endif;
        
        return $this->render('TicketBundle:Correo:respuestaValoracion.html.twig',array('valoracion'=>$valor,'id'=>$id,'yaValorado'=>$yaValorado,'ticket'=>$ticket));
        //echo "<script>alert('Este ticket ya fue valorado, gracias.');this.close();</script>";
    }
    
    public function comentarioValoracionAction(Request $request,$id,$valor) {
        $data=$request->request->all();
        $comentario=trim($data['comentario']);
        
        $em = $this->getDoctrine()->getManager();
        $ticket =  $em->getRepository('TicketBundle:Ticket')->find($id);
        
        if($comentario!=''):
            
            $ticket->setComentarioValoracion($comentario);
            $em->flush();  
            
            //envio correo de ticket asignado
            $html= $this->renderView('TicketBundle:Correo:valorado.html.twig',array('ticket' => $ticket));
            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Ticket valorado','Progis_Informacion',array($ticket->getNivelorganizacional()->getCorreo()),$html);
            $this->get('mailer')->send($correo); 
            
            return $this->redirect($this->generateUrl('progis_ticket_valorar',array('id'=>$id,'valor'=>$valor)));
            
        else:
            $this->get('session')->getFlashBag()->add('alert', 'El comentario no debe estar en blanco.');
        endif;
        
        return $this->render('TicketBundle:Correo:respuestaValoracion.html.twig',array('valoracion'=>$valor,'id'=>$id,'yaValorado'=>1,'ticket'=>$ticket));
    }
    
    public function listaAction()
    {
        $this->seguridad();

        $em = $this->getDoctrine()->getManager();

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        //busco los niveles donde el usuario tiene rol de ticket
        $ticketModelo = $this->get('ticketModelo');
        $idNivelesUsuarioRolTicket=$ticketModelo->idNivelesUsuarioRolTicket($perfil);

        $fa=\date("d-m-Y");
        $anioAnt = strtotime ( '-1 year' , strtotime ( $fa ) ) ;
        $anioAnt=new \DateTime(\date("d-m-Y",$anioAnt));
        
        //todos los tickets
        /*$dql = "select t from TicketBundle:Ticket t where t.nivelorganizacional in (:idnivel) and t.fechasolicitud>= :fechasolicitud and t.estatus!=4 order by t.estatus, t.fechasolicitud, t.horasolicitud ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $query->setParameter('fechasolicitud',$anioAnt);
        $tickets = $query->getResult();*/
        //fin

        
        //consulto tickets asignados
        $dql = "select p from TicketBundle:ProcesarTicket p join p.ticket t where t.nivelorganizacional in (:idnivel) and t.fechasolicitud>= :fechasolicitud";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $query->setParameter('fechasolicitud',$anioAnt);
        $ticketAsignado= $query->getResult();

        $array_ticketasignado=array();
        foreach ($ticketAsignado as $ta) {
            $array_ticketasignado[$ta->getTicket()->getId()]['usuario']=$ta->getResponsable()->getUsuario()->getPrimerNombre().' '.$ta->getResponsable()->getUsuario()->getPrimerApellido();
            $array_ticketasignado[$ta->getTicket()->getId()]['foto']=$ta->getResponsable()->getUsuario()->getFoto();
        }
        //fin
           
        $cantidad=  $this->cantidad($idNivelesUsuarioRolTicket);
        return $this->render('TicketBundle:Gestion:lista.html.twig',array('array_ticketasignado'=>$array_ticketasignado,'cantidad'=>$cantidad,'idRolTicket'=>implode(",",$idNivelesUsuarioRolTicket)));
        //return $this->render('TicketBundle:Gestion:lista.html.twig',array('array_ticketasignado'=>$array_ticketasignado,'tickets'=>$tickets,'cantidad'=>$cantidad,'idRolTicket'=>implode(",",$idNivelesUsuarioRolTicket)));
    }
    
    public function asignarAction($idticket){
        
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $ticket =  $em->getRepository('TicketBundle:Ticket')->find($idticket);
        $procesarTicket =  $em->getRepository('TicketBundle:ProcesarTicket')->find($idticket);
        
        //busco los niveles donde el usuario tiene rol de ticket
        $ticketModelo = $this->get('ticketModelo');
        $funcionModelo = $this->get('funcionModelo');
        $miembroTicket = $ticketModelo->miembroTicket($funcionModelo,$perfil);
        
        
        //categorias y subcategoriasusuario para la seccion de asignar
        $arraycatsub = $this->catsub($ticket->getNivelorganizacional()->getId());
        //fin

        //busco todas la unidades pertenecientes a la direccion para la seccion de reasignar
        $unidadesDireccion = $this->unidadesDireccion();
        //fin
        
        //busco la informacion del ticket asignado
        $datos=null;
        if(!empty($procesarTicket)):
            $datos['tecnico']=$procesarTicket->getResponsable()->getUsuario()->getId();
            $datos['subcat']=$procesarTicket->getSubcategoria()->getId();
            $datos['categoria']=$procesarTicket->getSubcategoria()->getCategoria()->getCategoria();
            $datos['nivel']=$procesarTicket->getSubcategoria()->getCategoria()->getNivelorganizacional()->getDescripcion();
        endif;

        //verifico si es reasignado
        $reasignado =  $em->getRepository('TicketBundle:Reasignado')->findByTicket($idticket);
        $deleteForm = $this->createDeleteForm($idticket);
        
        $sms=null;
        return $this->render('TicketBundle:Gestion:asignar.html.twig',array('delete_form' => $deleteForm->createView(),'reasignado'=>$reasignado,'ticket'=>$ticket,'arraycatsub'=>$arraycatsub,'miembroTicket'=>$miembroTicket,'sms'=>$sms,'unidadesDireccion'=>$unidadesDireccion,'procesarTicket'=>$procesarTicket,'datos'=>$datos,'perfil'=>$perfil));
    }
    
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TicketBundle:Ticket')->find($id);

        /*$dql = "select s from TicketBundle:Subcategoria s join s.categoria c where c.id= :id";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        $entities = $query->getResult();*/

        if($entity->getEstatus()!=1){
            $this->get('session')->getFlashBag()->add('alert', 'No se peude eliminar el ticket porque ya fue puesto en proceso.');
            return $this->redirect($this->generateUrl('progis_categoria'));
        }


        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Ticket eliminado con exito.');
        return $this->redirect($this->generateUrl('progis_ticket_lista'));
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('progis_ticket_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
    
    
    public function validarasignar($datos) {
        $sms=null;
        if($datos['subcat']==''):
            $sms .="Debe seleccionar una categoria<br>";
        endif;
        
        if($datos['tecnico']==''):
            $sms .="Debe selecionar un técnico<br>";
        endif;
        
        
        return $sms;
    }
    
    public function procesaAsignarAction(Request $request,$idticket)
    {
        $em = $this->getDoctrine()->getManager();
        $ticket =  $em->getRepository('TicketBundle:Ticket')->find($idticket);
        //verifico si es reasignado
        $reasignado =  $em->getRepository('TicketBundle:Reasignado')->findByTicket($idticket);
        $deleteForm = $this->createDeleteForm($idticket);
        
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');

        if(
                $rolGeneralNivel[$ticket->getNivelorganizacional()->getId()]['ROLE_PROGIS_TICKET_ADMIN']==true
                or $rolGeneralNivel[$ticket->getNivelorganizacional()->getId()]['ROLE_PROGIS_TICKET_TECNICO']==true 
                or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();

        //definiciones
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $procesarTicket =  $em->getRepository('TicketBundle:ProcesarTicket')->find($idticket);
        //fin
        
        //valido que no asigne un ticket en proceso
        if(!empty($procesarTicket) and $procesarTicket->getUbicacion()==2):
            $this->get('session')->getFlashBag()->add('alert', 'El ticket se encuentra en proceso por lo tanto no puede asignarlo.');
            return $this->redirect($this->generateUrl('progis_ticket_asignar', array('idticket' => $idticket)));
        endif;
        //fin
        
        //busco todos los usuarios miembros de tickets
        
        $funcionModelo = $this->get('funcionModelo');
        $ticketModelo = $this->get('ticketModelo');
        $miembroTicket = $ticketModelo->miembroTicket($funcionModelo,$perfil);
        $idNivelesUsuarioRolTicket=$ticketModelo->idNivelesUsuarioRolTicket($perfil);
        //fin
        
        $arraycatsub = $this->catsub($idNivelesUsuarioRolTicket);
        
        //recibo y valido datos
        $datos['subcat']=$request->get('subcat');
        if($datos['subcat']!=''):
            $subcat = $em->getRepository('TicketBundle:Subcategoria')->find($datos['subcat']);
            $datos['categoria']=$subcat->getCategoria()->getCategoria();     
            $datos['nivel']=$subcat->getCategoria()->getNivelorganizacional()->getDescripcion();     
        endif;

        $datos['tecnico']=$request->get('tecnico');
        $sms=$this->validarasignar($datos);
        //fin
        
        //busco todas la unidades pertenecientes a la direccion
        $unidadesDireccion = $this->unidadesDireccion();
        //fin
        
        if($sms!=null):
            return $this->render('TicketBundle:Gestion:asignar.html.twig',array('delete_form' => $deleteForm->createView(),'reasignado'=>$reasignado,'ticket'=>$ticket,'arraycatsub'=>$arraycatsub,'miembroTicket'=>$miembroTicket,'sms'=>$sms,'unidadesDireccion'=>$unidadesDireccion,'procesarTicket'=>$procesarTicket,'datos'=>$datos,'perfil'=>$perfil));
       
        else:
            
            $em = $this->getDoctrine()->getManager();

            $perfilTecnico =  $em->getRepository('UsuarioBundle:Perfil')->find($datos['tecnico']);
            $miembroEspacio = $em->getRepository('PrincipalBundle:Miembroespacio')->findBy(array("usuario"=>$datos['tecnico'],"nivelorganizacional"=>$perfilTecnico->getNivelorganizacional()));

            if(!empty($procesarTicket)):
                
                //si ya fue asignado una vez actualizo
                $query = $em->createQuery('update TicketBundle:Procesarticket x set x.subcategoria= :subcat, x.responsable= :tecnico, x.tipotiempo= :tipotiempo, x.tiempoestimado= :tiempo WHERE x.ticket= :ticket');             
                $query->setParameter('subcat', $subcat);
                $query->setParameter('tecnico', $miembroEspacio[0]);
                $query->setParameter('ticket', $ticket);
                $query->setParameter('tipotiempo', $subcat->getTipotiempo());
                $query->setParameter('tiempo', $subcat->getTiempoatencion());
                $query->execute(); 
                
                
                
            else:

                //si no fue asignado asigno
                $a=new ProcesarTicket;
                $a->setTicket($ticket);
                $a->setResponsable($miembroEspacio[0]);
                $a->setSubcategoria($subcat);
                $a->setTiempoestimado($subcat->getTiempoatencion());
                $a->setRetardo(false);
                $a->setCorreoretardo(false);
                $a->setTipotiempo($subcat->getTipotiempo());
                $a->setUbicacion(1);
                $a->setRegistradopor($perfil);
                
                $em->persist($a);
                $em->flush(); 
                


                //envio correo al solicitante
                $html= $this->renderView('TicketBundle:Correo:asignadoSolicitante.html.twig',array('ticket' => $ticket,'subcat'=>$subcat));
                $funcion=$this->get("funcionModelo");
                $correo=$funcion->correo('Tu ticket fue asignado a un técnico','Progis_Informacion',array($ticket->getSolicitante()->getUser().'@telesurtv.net'),$html);
                $this->get('mailer')->send($correo); 
                //fin envio correo
                
            endif;
            
            //cambio estatus del ticket
            $ticket->setEstatus(2);
            $em->flush(); 
            //$em->flush();
                
            if(empty($procesarTicket) or $procesarTicket->getResponsable()->getId()!=$miembroEspacio[0]->getId()):
                //envio correo de ticket asignado
                $html= $this->renderView('TicketBundle:Correo:asignado.html.twig',array('ticket' => $ticket,'subcat'=>$subcat));
                $funcion=$this->get("funcionModelo");
                $correo=$funcion->correo('Tienes un ticket asignado','Progis_Informacion',array($miembroEspacio[0]->getUsuario()->getUser()->getUsername().'@telesurtv.net'),$html);
                $this->get('mailer')->send($correo); 
                //fin envio correo 
            endif;

            
            $this->get('session')->getFlashBag()->add('notice', 'El ticket se ha asignado al técnico '.ucfirst($miembroEspacio[0]->getUsuario()->getPrimerNombre().' '.$miembroEspacio[0]->getUsuario()->getPrimerApellido()));
            return $this->redirect($this->generateUrl('progis_ticket_procesarTicket'));
            //return $this->redirect($this->generateUrl('progis_ticket_asignar', array('idticket' => $idticket)));
        endif;
    }
    
    public function procesaReasignarAction(Request $request,$idticket)
    {
        $em = $this->getDoctrine()->getManager();
        $ticket =  $em->getRepository('TicketBundle:Ticket')->find($idticket);
        
        $trabajoRealizado =  $em->getRepository('PrincipalBundle:TrabajoTicket')->findByTicket($idticket);
        
        if(!empty($trabajoRealizado)){
            $this->get('session')->getFlashBag()->add('alert','El ticket no puede ser reasignado porque la tarjeta ya fue movida.');
            return $this->redirect($this->generateUrl('progis_ticket_asignar',array('idticket'=>$idticket))); 
        }
        
        $session = $this->getRequest()->getSession();
        $rolGeneralNivel=$session->get('rolGeneralNivel');

        if(
                $rolGeneralNivel[$ticket->getNivelorganizacional()->getId()]['ROLE_PROGIS_TICKET_ADMIN']==true
                or $rolGeneralNivel[$ticket->getNivelorganizacional()->getId()]['ROLE_PROGIS_TICKET_TECNICO']==true 
                or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
        
        //valido que se seleccione una unidad
        if($request->get("idNivel")==null){
            $this->get('session')->getFlashBag()->add('alert','El ticket no fue reasignado porque no selecciono ninguna unidad.');
            return $this->redirect($this->generateUrl('progis_ticket_asignar',array('idticket'=>$idticket))); 
        }
        
        $procesarTicket =  $em->getRepository('TicketBundle:ProcesarTicket')->find($idticket);
        
        //valido que no se reasigne si esta en proceso
        if(!empty($procesarTicket) and $procesarTicket->getUbicacion()==2):
            $this->get('session')->getFlashBag()->add('alert', 'El ticket se encuentra en proceso por lo tanto no puede reasignarlo.');
            return $this->redirect($this->generateUrl('progis_ticket_asignar', array('idticket' => $idticket)));
        endif;
        
        
        //actualizo la nueva unidad del ticket
        $nivelAnterior=$ticket->getNivelorganizacional();
        $nivel =  $em->getRepository('UsuarioBundle:Nivelorganizacional')->find($request->get("idNivel"));
        $ticket->setNivelorganizacional($nivel);
        $ticket->setEstatus(3);
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $miembroEspacio =  $em->getRepository('PrincipalBundle:Miembroespacio')->findBy(array('usuario'=>$perfil,'nivelorganizacional'=>$ticket->getNivelorganizacional()));
        
        $em->flush();

        //borro si ya tenia algo en proceso
        if(!empty($procesarTicket)):
            $em->remove($procesarTicket);
            $em->flush($miembroEspacio);
        endif;

        //agrego en la tabla reasignado
        $reasignado=new Reasignado();
        $reasignado->setTicket($ticket);
        $reasignado->setResponsable($perfil);
        $reasignado->setNivelorganizacional($nivelAnterior);
        $em->persist($reasignado);
        $em->flush();
        
        //envio correo
        $html= $this->renderView('TicketBundle:Correo:solicitud.html.twig',array('ticket' => $ticket));
        $funcion=$this->get("funcionModelo");
        $correo=$funcion->correo('Tienes una nueva solicitud','Progis_Informacion',array($ticket->getNivelorganizacional()->getCorreo()),$html);
        $this->get('mailer')->send($correo); 
        //fin envio correo
            
        
        $this->get('session')->getFlashBag()->add('notice', 'El ticket se reasigno correctamente.');
        return $this->redirect($this->generateUrl('progis_ticket_lista')); 
      
    }
}
