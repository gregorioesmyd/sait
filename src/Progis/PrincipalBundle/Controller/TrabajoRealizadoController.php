<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progis\ProyectoBundle\Entity\Miembroproyecto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TrabajoRealizadoController extends Controller
{
    
    public function trabajoRealizadoFiltroAction()
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        
        $funcionModelo = $this->get('funcionModelo');
        $miembroespacio=$funcionModelo->miembrosMiEspacioEscalera($perfil);
        //fin
        $usuarios=array();$ids=array();
        foreach ($miembroespacio as $m) {
            $usuarios[]=$m->getUsuario();
        }
        $usuarios=array_unique($usuarios);
        
        //niveles
        $dql = "select x from UsuarioBundle:Nivelorganizacional x where x.codigo like :codNiv";
        $query = $em->createQuery($dql);
        $query->setParameter("codNiv",$perfil->getNivelorganizacional()->getCodigo()."%");
        $niveles = $query->getResult();
        
        $arrayNivel=array();
        foreach ($niveles as $n) {
            $arrayNivel[]=$n->getId();
        }
        
        
        $dql = "select x from UsuarioBundle:Perfil x join x.nivelorganizacional no join x.user u where no.id in (:nivel) and x.jefe=true and u.isActive=true";
        $query = $em->createQuery($dql);
        $query->setParameter("nivel",$arrayNivel);
        $perfil = $query->getResult();
        

        return $this->render('PrincipalBundle:TrabajoRealizado:filtro.html.twig',array('usuarios'=>$usuarios,'niveles'=>$niveles,'perfil'=>$perfil));
    }
    
    public function miTrabajoRealizadoFiltroAction()
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        return $this->render('PrincipalBundle:TrabajoRealizado:miFiltro.html.twig',array('perfil'=>$perfil));
    }


    function diasEntreFechas($inicio, $fin)
    {

        $fecha1 = new \DateTime($inicio);
        $fecha2 = new \DateTime($fin);
        $fecha = $fecha1->diff($fecha2);
        
        //printf('%d años, %d meses, %d días, %d horas, %d minutos', $fecha->y, $fecha->m, $fecha->d, $fecha->h, $fecha->i);
        
        return $fecha->days;
    }
    
    public function indexAction(Request $request)
    {
        $data=$request->request->All();
        
        if(!empty($data) and ($data['fd']=='' or $data['fh']=='' or $data['nivel']=='')):
            $this->get('session')->getFlashBag()->add('alert', 'Debe llenar todos los campos.');
            return $this->redirect($this->generateUrl('progis_principal_trabajo_realizado_filtro'));
        endif;
        
        $em = $this->getDoctrine()->getManager();
        $fa=new \DateTime(\date("d-m-Y G:i:s"));

        //BUSCO QUIENES NO DEBEN SALIR EN REPORTE
        $dql = "select me from PrincipalBundle:Miembroespacio me where me.activo=true and me.mostrarEnReporte=false and me.nivelorganizacional= :nivel";
        $query = $em->createQuery($dql);


        $query->setParameter('nivel',$data['nivel']);
        $miembroespacio = $query->getResult();
        $usuarios[]=0;
        foreach ($miembroespacio as $me) {
            $usuarios[]=$me->getUsuario()->getId();

            echo $me->getUsuario()->getPrimerNombre();
        }
        $dql = "select t from PrincipalBundle:TrabajoRealizado t join t.responsable me join me.usuario u where t.fecha>= :fd and t.fecha<= :fh and u.nivelorganizacional = :nivel and me.activo=true and me.usuario not in (:noMostrarReporte) order by t.responsable, t.tipo, t.fecha ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('nivel',$data['nivel']);
        $query->setParameter('noMostrarReporte',$usuarios);
        $query->setParameter('fd',$data['fd']);
        $query->setParameter('fh',$data['fh']);
        $trabajoRealizado = $query->getResult();
        
        //calculo la cantidad de horas respecto a la fecha y separo los usuarios repetidos
        $dias=$this->diasEntreFechas($data['fd'], $data['fh']);
        
        
        $usuarios=array();
        foreach ($trabajoRealizado as $t) {
            
            
            $arrayDiasJornada=explode("-",$t->getResponsable()->getJornadalaboral()->getDias());
            $cantHoraDiaria=$t->getResponsable()->getJornadalaboral()->getHorasDiarias();

            $contDiasEnJornada=0;
            for($i=0;$i<=$dias;$i++){
                //voy avanzando desde la fecha de inicio a la de fin para saber que dia de la semana es y si esta en su jornada laboral
                $nuevafecha = strtotime ( '+'.$i.' day' , strtotime ( $data['fd'] ) ) ;
                

                $diaSemana=date("N",$nuevafecha);
                if (in_array($diaSemana, $arrayDiasJornada)) {
                    $contDiasEnJornada++;
                }
            }

            $totalHoras=$cantHoraDiaria*$contDiasEnJornada;
            
            
            
            $usuarios[$t->getResponsable()->getUsuario()->getId()]['usuario']=$t->getResponsable()->getUsuario();
            $usuarios[$t->getResponsable()->getUsuario()->getId()]['horasLaborales']=$totalHoras;
            
            
        }


        return $this->render('PrincipalBundle:TrabajoRealizado:index.html.twig',array('trabajoRealizado'=>$trabajoRealizado,'data'=>$data,'usuarios'=>$usuarios));
    }
    
    public function miTrabajoRealizadoAction(Request $request,$pdf,$id)
    {
        $em = $this->getDoctrine()->getManager();
        //$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($id);
        
        $data=$request->request->All();
        
        if(!empty($data) and ($data['fd']=='' or $data['fh']=='')):
            $this->get('session')->getFlashBag()->add('alert', 'Debe llenar todos los campos.');
            return $this->redirect($this->generateUrl('progis_principal_trabajo_realizado_filtro'));
        endif;
        
        $em = $this->getDoctrine()->getManager();
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $dql = "select t from PrincipalBundle:TrabajoRealizado t join t.responsable me where t.fecha>= :fd and t.fecha<= :fh and me.usuario= :usuario order by t.tipo, t.fecha ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $query->setParameter('fd',$data['fd']);
        $query->setParameter('fh',$data['fh']);
        $trabajoRealizado = $query->getResult();
        
        
        //calculo la cantidad de horas respecto a la fecha y separo los usuarios repetidos
        $dias=$this->diasEntreFechas($data['fd'], $data['fh']);
        
        $usuarios=array();
        foreach ($trabajoRealizado as $t) {
            
            
            $arrayDiasJornada=explode("-",$t->getResponsable()->getJornadalaboral()->getDias());
            $cantHoraDiaria=$t->getResponsable()->getJornadalaboral()->getHorasDiarias();

            $contDiasEnJornada=0;
            for($i=0;$i<=$dias;$i++){
                //voy avanzando desde la fecha de inicio a la de fin para saber que dia de la semana es y si esta en su jornada laboral
                $nuevafecha = strtotime ( '+'.$i.' day' , strtotime ( $data['fd'] ) ) ;
                

                $diaSemana=date("N",$nuevafecha);
                if (in_array($diaSemana, $arrayDiasJornada)) {
                    $contDiasEnJornada++;
                }
            }

            $totalHoras=$cantHoraDiaria*$contDiasEnJornada;
            
            
            
            $usuarios[$t->getResponsable()->getUsuario()->getId()]['usuario']=$t->getResponsable()->getUsuario();
            $usuarios[$t->getResponsable()->getUsuario()->getId()]['horasLaborales']=$totalHoras;
            
            
        }

        if($pdf=='false'):
            return $this->render('PrincipalBundle:TrabajoRealizado:miIndex.html.twig',array('trabajoRealizado'=>$trabajoRealizado,'data'=>$data,'usuarios'=>$usuarios,'id'=>$id));

        elseif($pdf=='true'):
            $html= $this->render('PrincipalBundle:TrabajoRealizado:miIndexPdf.html.twig',array('trabajoRealizado'=>$trabajoRealizado,'data'=>$data,'usuarios'=>$usuarios));

            include("libs/MPDF/mpdf.php");
            $mpdf=new \mPDF();
            //izq - der - arr - aba
            //$mpdf->AddPage('P','','','','',10,10,0,0);
            $mpdf->AddPage('P','','','','',10,10,10,10,18,12);
            //$stylesheet = file_get_contents('libs/bootstrap3/css/bootstrap.min.css');
            $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text

            $mpdf->WriteHTML($html->getContent());
            $mpdf->Output("miTrabajoRealizado".".pdf","D");
            exit;
            die;
        endif;
        
    }
    
    
    
    
  
}
/*

 */