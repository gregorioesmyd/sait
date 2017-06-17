<?php

namespace Progis\ProyectoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Administracion\UsuarioBundle\Resources\Misclases\Funcion;
use Symfony\Component\HttpFoundation\Request;

use Progis\ProyectoBundle\Resources\Misclases\Proyectofunciones;


class DefaultController extends Controller
{
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_PROYECTO_GENERAL')) {
            return $this->redirect($this->generateUrl('proyecto_general'));
        }
        else if ($this->get('security.context')->isGranted('ROLE_PROYECTO')) {
            return $this->redirect($this->generateUrl('proyecto'));
        }
    }
    
    public function buscarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $palabra=$request->get('palabra');
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $dql = "select x from ProyectoBundle:Proyecto x join x.nivelorganizacional no where no.id = :idnivel and (lower(x.nombre) like :palabra or lower(x.descripcion) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $proyecto = $query->getResult();    
        
        $dql = "select x from ProyectoBundle:Tarea x join x.proyecto p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.nombre) like :palabra or lower(x.descripcion) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $tarea = $query->getResult();  
        
        
        $dql = "select x from ProyectoBundle:Actividad x join x.tarea t join t.proyecto p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.descripcion) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $actividad = $query->getResult();
        
        
        $dql = "select x from ProyectoBundle:Comentario x join x.proyectoId p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.comentario) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $comentarioproyecto = $query->getResult();       
        
        $dql = "select x from ProyectoBundle:Comentarioactividad x join x.actividad a join a.tarea t join t.proyecto p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.comentario) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $comentarioactividad = $query->getResult(); 
        
        $dql = "select x from ProyectoBundle:Documento x join x.proyectoId p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.descripcion) like :palabra or lower(x.archivo) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $documentoproyecto = $query->getResult(); 
        
        $dql = "select x from ProyectoBundle:Documentoactividad x join x.actividadId a join a.tarea t join t.proyecto p join p.nivelorganizacional no where no.id = :idnivel and (lower(x.descripcion) like :palabra or lower(x.archivo) like :palabra)";
        $query = $em->createQuery($dql);
        $query->setParameter('palabra',"%".$palabra."%");
        $query->setParameter('idnivel',$perfil->getNivelorganizacional()->getId());
        $documentoactividad= $query->getResult(); 

        
        return $this->render('ProyectoBundle:Default:buscar.html.twig', array(
            'actividad'=>$actividad,
            'tarea'=>$tarea,
            'proyecto'=>$proyecto,
            'comentarioproyecto'=>$comentarioproyecto,
            'comentarioactividad'=>$comentarioactividad,
            'documentoproyecto'=>$documentoproyecto,
            'documentoactividad'=>$documentoactividad,
            'palabra'=>$palabra
            
        ));
    }
    
    public function procesoAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $integrantes=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        $responsable=null;
        foreach ($integrantes as $v) {
            $responsable[]=$v->getId();
        }

        $dql = "select x from ProyectoBundle:Actividad x join x.responsable r where x.ubicacion=2 and x.responsable in (:responsable)";
        $query = $em->createQuery($dql);
        $query->setParameter('responsable',$responsable);
        $actividades = $query->getResult();

        return $this->render('ProyectoBundle:Default:proceso.html.twig', array(
            'actividades'=>$actividades,
            'integrantes'=>$integrantes
        ));
    }
    
    public function revisarAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $integrantes=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
       
        $responsable=null;
        foreach ($integrantes as $v)
            $responsable[]=$v->getId();
        
        $dql = "select x from ProyectoBundle:Actividad x where x.ubicacion=3 and x.responsable in (:responsable)";
        $query = $em->createQuery($dql);
        $query->setParameter('responsable',$responsable);
        $revision = $query->getResult();
        
        return $this->render('ProyectoBundle:Default:revisar.html.twig', array(
            'revision'=>$revision,
            'integrantes'=>$integrantes
        ));
    }
    
    public function pendienteAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $integrantes=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        $responsable=null;
        foreach ($integrantes as $v) {
            $responsable[]=$v->getId();
        }
        
        
        $dql = "select x from ProyectoBundle:Actividad x join x.tarea t where x.ubicacion=1 and x.responsable in (:responsable) order by t.fechafinestimada ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('responsable',$responsable);
        $actpendiente = $query->getResult();

        return $this->render('ProyectoBundle:Default:pendiente.html.twig', array(
            'actpendiente'=>$actpendiente,
            'integrantes'=>$integrantes
        ));
    }
}
