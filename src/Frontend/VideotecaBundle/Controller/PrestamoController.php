<?php

namespace Frontend\VideotecaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\VideotecaBundle\Entity\DetallePrestamo;
use Frontend\VideotecaBundle\Entity\Prestamo;
use Frontend\VideotecaBundle\Entity\TmpPrestamo;

use Frontend\VideotecaBundle\Form\PrestamoType;

/**
 * Prestamo controller.
 *
 */
class PrestamoController extends Controller
{

    /**
     * Lists all Prestamo entities.
     *
     */
    public function indexAction()
    {
       // $em = $this->getDoctrine()->getManager();

      //  $entities = $em->getRepository('VideotecaBundle:DetallePrestamo')->findAll();

        return $this->render('VideotecaBundle:Prestamo:index.html.twig');
    }
    
    /**
     * Creates a new Prestamo entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 
        $entity = new Prestamo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        $dqle = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true";
        $sql = $em->createQuery($dqle);
        $user = $sql->getResult();
        //Valido que el campo de factura no venga vacio
        //obtengo el id del usuario que inicio sesion para mostrar los prestamos que tiene
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $prestamista = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        //Fin
        //consulto los prestamos que estan sin procesar del usuario loggeado
        $tmpPrestamo= $em->getRepository('VideotecaBundle:TmpPrestamo')->findByPrestamista($idusuario);
        //FIN
        $id_select = $request->request->get('id_user');
        $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$id_select";
        $query = $em->createQuery($dql);
        $solicitante = $query->getResult();
        $dependencias = $em->getRepository('UsuarioBundle:Nivelorganizacional')->findAll();

         if($entity->getFactura() == ''){
            $this->get('session')->getFlashBag()->add('alert', 'Debe indicar un numero de factura..');
               return $this->render('VideotecaBundle:Prestamo:new.html.twig', array(
                                        'entities'           => $tmpPrestamo,
                                        'user'               => $user,
                                        'datos'              => $solicitante[0],
                                        'dependencias'       => $dependencias,
                                        'form'               => $form->createView()
                                        ));
            }
        //Fin

        if ($form->isValid()) {
            //obtengo el id del usuario que inicio sesion para mostrar los prestamos que tiene
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $user = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            //Fin
            //
            //consulto los prestamos que estan sin procesar del usuario loggeado
            $tmpPrestamo= $em->getRepository('VideotecaBundle:TmpPrestamo')->findByPrestamista($idusuario);

            //FIN
            //
            //obtengo el id del id,dependencia y extension del usuario que solicito el prestamo
            $userSolicitante= $request->request->get('id_user');
            $id_cinta= $request->request->get('id_user');
            $dependencia= $request->request->get('dependencia');
            $extension= $request->request->get('extension');
            //Fin
            //
            //Guardo la informacion del prestamo
            $user_solicitante=$em->getRepository('UsuarioBundle:Perfil')->find($userSolicitante);
            $fecha_prestamo = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));

            $prestamo = new Prestamo();
            $prestamo->setUserSolicitante($user_solicitante);
            $prestamo->setUserPrestamista($user);
            $prestamo->setFactura($entity->getFactura());


            $em->persist($prestamo);
            $em->flush();

            foreach ($tmpPrestamo as $value) 
            {
               $Hprestamo = new DetallePrestamo();
               $Hprestamo->setIdCinta($value->getCodigo());
               $Hprestamo->setIdPrestamo($prestamo);
               $Hprestamo->setFechaPrestamo($fecha_prestamo);                   
               $em->persist($Hprestamo);
               $em->flush();
           }


           //actualizo el campo dependencia y extension del usuario solicitante en la tabla usuario
           $update = "UPDATE UsuarioBundle:Perfil a SET a.nivelorganizacional = :dependencia, a.extension= :extension WHERE a.id = :id";
           $sql = $em->createQuery($update);
           $sql->setParameter('id', $user_solicitante);
           $sql->setParameter('dependencia', $dependencia);
           $sql->setParameter('extension', $extension);
           $sql->execute();

           $dql = "DELETE from VideotecaBundle:TmpPrestamo a where a.prestamista = :id";
           $query = $em->createQuery($dql);
           $query->setParameter('id', $idusuario);
           $query->execute();

           //actualizo el Status de la cinta que se presto
            foreach ($tmpPrestamo as $value) 
            {

               $update = "UPDATE VideotecaBundle:WrapperCinta\Cinta a SET  a.status= :status WHERE a.id = :id";
               $sql = $em->createQuery($update);
               $sql->setParameter('id',$value->getCodigo());
               $sql->setParameter('status', 3);
               $sql->execute();
           }


           $entity = $em->getRepository('VideotecaBundle:DetallePrestamo')->find($Hprestamo->getId());
           $this->get('session')->getFlashBag()->add('notice', 'TU PRESTAMO SE HA REALIZADO EXITOSAMENTE');
           return $this->render('VideotecaBundle:Prestamo:show.html.twig', array(
                        'entity'      => $entity,
                        ));
           
       }
   }

    /**
     * Creates a form to create a Prestamo entity.
     *
     * @param Prestamo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Prestamo $entity)
    {
        $form = $this->createForm(new PrestamoType(), $entity, array(
            'action' => $this->generateUrl('prestamo_create'),
            'method' => 'POST',
            ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Prestamo entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->request->get('user_solicitante')){
            //obtengo el id del usuario que inicio sesion para mostrar los prestamos que tiene
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
         //Fin
        //consulto los prestamos que estan sin procesar del usuario loggeado
            $entities = $em->getRepository('VideotecaBundle:TmpPrestamo')->findByPrestamista($idusuario);
        //FIN
            $entity = new Prestamo();

            $form   = $this->createCreateForm($entity);

            $id_select=$request->request->get('user_solicitante');

            $em = $this->getDoctrine()->getManager();

            $dependencias = $em->getRepository('UsuarioBundle:Nivelorganizacional')->findAll();

            $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$id_select";
            $query = $em->createQuery($dql);
            $solicitante = $query->getResult();


            $dqle = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true";
            $sql = $em->createQuery($dqle);
            $user = $sql->getResult();
            return $this->render('VideotecaBundle:Prestamo:new.html.twig', array(
                'entities'           => $entities,
                'user'               => $user,
                'datos'              => $solicitante[0],
                'dependencias'       => $dependencias,
                'form'               => $form->createView()
                ));
        }
        
        $entity = new Prestamo();
        $form   = $this->createCreateForm($entity);
        
        $dqle = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true";
        $sql = $em->createQuery($dqle);
        $user = $sql->getResult();

        return $this->render('VideotecaBundle:Prestamo:new.html.twig', array(
            'entity'        => $entity,
            'form'          => $form->createView(),
            'user'          => $user,
            ));
    }

    /**
     * Finds and displays a Prestamo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:DetallePrestamo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prestamo entity.');
        }

        return $this->render('VideotecaBundle:Prestamo:show.html.twig', array(
            'entity'      => $entity,
            ));
    }

    
    public function cancelarAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        if($this->getRequest()->isXmlHttpRequest())
        {
            $num_id = $request->request->get('id');
      
        foreach ($num_id as $value){
           
        //Consulto las cintas que fueron seleccionadas para eliminar su prestamo
            $entity = $em->getRepository('VideotecaBundle:TmpPrestamo')->find($value);

            $id_cinta = $entity->getCodigo();
            //Actualizamos el status de la cinta
            $update = "UPDATE VideotecaBundle:WrapperCinta\Cinta a SET  a.status= :status WHERE a.id = :id";
            $sql = $em->createQuery($update);
            $sql->setParameter('id', $id_cinta);
            $sql->setParameter('status', 1);
            $sql->execute(); 
            
            $em->remove($entity);
            $em->flush();
        //Fin
          }
            $data = array('valid' => 'si');
            return new JsonResponse($data);
        } 
    }

 
    public function devolucionAction($codigo){
        
       if(!empty($codigo)){

            $em = $this->getDoctrine()->getManager();
           //Actualizamos el status de la cinta
            $update = "UPDATE VideotecaBundle:WrapperCinta\Cinta a SET  a.status= :status WHERE a.codigo = :codigo";
            $sql = $em->createQuery($update);
            $sql->setParameter('codigo', $codigo);
            $sql->setParameter('status', 1);
            $sql->execute();   
        }
        $this->get('session')->getFlashBag()->add('notice', 'LA DEVOLUCIÓN DE LA CINTA FUE REALIZADO EXITOSAMENTE');
        return $this->redirect($this->generateUrl('videoteca_prestamo'));
        
    }

    public function tmpPrestamoAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            //Consulto los datos de la cinta seleccionada
            $cinta = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($id);
            //Fin
            //obtengo el codigo de la cinta
            $codigo = $cinta->getCodigo();
            //Fin
            //preparo los datos a insertarse en la tabla tmpPrestamo
            $fecha_operacion = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $user = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            //Fin
            //Inserto los datos en la BD tmpPrestamo
            $temporalpres = new TmpPrestamo();
            $temporalpres->setCodigo($cinta);
            $temporalpres->setCreado($fecha_operacion);
            $temporalpres->setPrestamista($user);
            $em->persist($temporalpres);
            $em->flush();
            //Fin
            //Consulto las cintas que estan para prestarse segun el usuario logueado
            $prestamo= $em->getRepository('VideotecaBundle:TmpPrestamo')->findByPrestamista($idusuario);
            //Fin
            foreach ($prestamo as $value) 
            {
                $id= $value->getId();
                $update = "UPDATE VideotecaBundle:WrapperCinta\Cinta a SET  a.status= :status WHERE a.id = :id";
                $sql = $em->createQuery($update);
                $sql->setParameter('id',$value->getCodigo());
                $sql->setParameter('status', 2);
                $sql->execute();
            }
                $data = array("id"=>$id,"codigo"=>$codigo);
                return new JsonResponse($data);
          } else {
            $response = new Response();
            $response->setContent('Error! No se puede acceder al metodo esta manera.');
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'text/html');
            $response->send();
        }
    }

}
