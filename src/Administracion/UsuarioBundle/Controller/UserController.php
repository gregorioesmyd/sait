<?php

namespace Administracion\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Administracion\UsuarioBundle\Entity\User;
use Administracion\UsuarioBundle\Form\UserType;

use Administracion\UsuarioBundle\Entity\Perfil;
use Administracion\UsuarioBundle\Form\PerfilType;

use Administracion\UsuarioBundle\Entity\Cambioclave;
use Administracion\UsuarioBundle\Form\CambioclaveType;


use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    function quitarAcentos($string){
	return strtoupper(str_replace(array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ'),array('a','e','i','o','u','A','E','I','O','U','ñ','Ñ'),$string));
    }
    
    public function actualizarFotoAction($id){
            
        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($id);
        return $this->render('UsuarioBundle:User:actualizarFoto.html.twig', array(
            'p' => $perfil,
        ));
    }

   public function RespuestaencuestaAction($respuesta)
    {
        $em = $this->getDoctrine()->getManager();
        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $username = $this->get('security.context')->getToken()->getUser()->getUsername();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);


        $perfil->setBolsaComida($respuesta);
        $perfil->setActivarProceso(false);

        $em->persist($perfil);
        $em->flush();


       if($respuesta=='true')$respuesta='Estoy de Acuerdo'; else $respuesta='No Estoy de Acuerdo';


        $message = \Swift_Message::newInstance()
        ->setSubject('Consulta-RRHH')
        ->setFrom("aplicaciones@telesurtv")
        ->setTo($username."@telesurtv.net")
        ->setBody( "Tu respuesta a la pregunta '¿Autoriza usted a la Dirección General de Recursos Humanos a descontar la cantidad de 12.338,76, Bs por concepto de compra bolsa de alimentos de Abasto Bicentenario?' fue '".$respuesta."'" ,'text/html');

        $this->get('mailer')->send($message);


        $this->get('session')->getFlashBag()->add('notice', 'Gracias por tu respuesta.');
        return $this->redirect($this->generateUrl('usuario_homepage'));
        
    }
    
    public function procesarActualizarFotoAction(Request $request,$id)
    {
        $datos=$request->request->all();
        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($id);


        if(empty($_FILES['archivo']['name'])):
            $this->get('session')->getFlashBag()->add('alert', 'Debe cargar una imágen.');
            return $this->redirect($this->generateUrl('user_actualizarFoto',array('id'=>$id)));

        else:

            $tamaño=number_format($_FILES['archivo']['size'],0, '', '')/1000;

            $nombre=$_FILES['archivo']['name'];
            $nombreLargo=explode(".", $nombre);
            $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
            $extension = $nombreLargo[1];

            //validaciones
                $extensiones=array('jpg','jpeg','JPG','JPEG');
                if (!in_array($extension,$extensiones)) {
                    $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                    return $this->redirect($this->generateUrl('user_actualizarFoto',array('id'=>$id)));
                }

                
            //fin
            if(move_uploaded_file($_FILES['archivo']['tmp_name'],'/var/www/uploads/personalTelesur/'.$perfil->getCedula().'.jpg') )
            {
                $perfil->setFoto('/uploads/personalTelesur/'.$perfil->getCedula().'.jpg');
                $em->flush();
            }

        endif;
            
        $this->get('session')->getFlashBag()->add('notice', 'Imagen actualizada exitosamente.');
        return $this->redirect($this->generateUrl('user_actualizarFoto',array('id'=>$id)));
    }
    

    public function actualizarDatosAction(Request $request)
    {
        $datos=$request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);
        $familia = $em->getRepository('UsuarioBundle:Familia')->findByTrabajador($IdUsuario);
        
        $info['primerNombre']=$perfil->getPrimerNombre();
        $info['primerApellido']=$perfil->getPrimerApellido();
        $info['segundoNombre']=$perfil->getSegundoNombre();
        $info['segundoApellido']=$perfil->getSegundoApellido();
        $info['cedula']=$perfil->getCedula();
        
        $form   = $this->createForm(new PerfilType(), $perfil);

        return $this->render('UsuarioBundle:User:actualizarDatos.html.twig', array(
            'usuario' => $perfil,
            'form' => $form->createView(),
            'info'=>$info,
            'familia'=>$familia
        ));
    }
    
    public function procesarActualizarDatosAction(Request $request)
    {
        $datos=$request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);
        $familia = $em->getRepository('UsuarioBundle:Familia')->findByTrabajador($IdUsuario);
        if(!empty($familia)):
            $hcm=$datos['hcm'];
        endif;
        
        $info['primerNombre']=$perfil->getPrimerNombre();
        $info['primerApellido']=$perfil->getPrimerApellido();
        $info['segundoNombre']=$perfil->getSegundoNombre();
        $info['segundoApellido']=$perfil->getSegundoApellido();
        $info['cedula']=$perfil->getCedula();
        
        $form   = $this->createForm(new PerfilType(), $perfil);
        $form->handleRequest($request);
        
        if($form->isValid()){
            
            $perfil->setPrimerNombre($info['primerNombre']);
            $perfil->setPrimerApellido($info['primerApellido']);
            $perfil->setSegundoNombre($info['segundoNombre']);
            $perfil->setSegundoApellido($info['segundoApellido']);
            $perfil->setCedula($info['cedula']);
            $perfil->setActivarProceso(false);

            if(!empty($familia)):
                foreach ($familia as $f) {
                    $f->setAlerenfer($hcm[$f->getIdFamiliar()]['alerenfer']);
                    if($f->getParentesco()=='H' and isset($hcm[$f->getIdFamiliar()]['especial'])):
                        $f->setEspecial($hcm[$f->getIdFamiliar()]['especial']);    
                        $f->setAsistePlanVacacional($hcm[$f->getIdFamiliar()]['plan']);     
                        $f->setTallaCamisa($hcm[$f->getIdFamiliar()]['tallaCamisa']);
			$f->setTallaZapatos($hcm[$f->getIdFamiliar()]['tallaZapatos']);  
                    endif;
                    $em->flush();
                }
            endif;
            
            $em->persist($perfil);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Tus datos fueron actualizados exitosamente, puedes volver a esta pagina desde el menú de aplicaciones en la sección "Acutalizar datos".');
            return $this->redirect($this->generateUrl('user_actualizarDatos'));

        }else $this->get('session')->getFlashBag()->add('alert', 'Error en formulario, por favor verifique.');

        return $this->render('UsuarioBundle:User:actualizarDatos.html.twig', array(
            'usuario' => $perfil,
            'form' => $form->createView(),
            'info'=>$info,
            'familia'=>$familia
        ));
    }
    
    public function ajaxActualizarDatosAction($id){
        
        $em = $this->getDoctrine()->getManager();
        $ciudades = $em->getRepository('UsuarioBundle:Ciudad')->findByEstado($id);
        $lista['options'][]="Seleccione...";
        foreach ($ciudades as $c) {
            $lista['options'][]= $c->getCiudad().'-'.$c->getId();;
        }
        
        return new JsonResponse($lista);
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        /*$dql = "select p from UsuarioBundle:Perfil p where p.primerNombre like :pn0 or p.primerApellido like :pa0";
        $query = $em->createQuery($dql);
        $query->setParameter('pn0',"%JHOAN%");
        $query->setParameter('pn0',"%VALERA%");
        $query->setParameter('pa0',"%JHOAN%");
        $query->setParameter('pa0',"%VALERA%");
        $procesarEnProceso = $query->getResult();
        
        foreach ($procesarEnProceso as $value) {
            echo $value->getPrimerNombre().' '.$value->getPrimerApellido().'<br>';
            
        }
        die;*/

        
        
        
        $entities = $em->getRepository('UsuarioBundle:Perfil')->findAll();

        return $this->render('UsuarioBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function notificarAction(Request $request)
    {
        $datos=$request->request->all();
        $datos=$datos['form'];

        $message = \Swift_Message::newInstance()     // we create a new instance of the Swift_Message class
            ->setSubject('CLAVE INTRANET')     // we configure the title
            ->setFrom('intranet@telesurtv.net')     // we configure the sender
            ->setTo("aplicaciones@telesurtv.net")     // we configure the recipient
            ->setBody("
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                El usuario ".$datos['nombre']." ".$datos['apellido']." de cédula ".$datos['cedula']." y extensión ".$datos['extension']." tiene la siguiente solicitud: ".$datos['comentario']."

            ", 'text/html');

            $this->get('mailer')->send($message);     // then we send the message.
            //FIN CORREO
            $this->get('session')->getFlashBag()->add('notice', 'Se ha enviado un correo a la Unidad de Aplicaciones y Desarrollo, pronto nos comunicaremos contigo!!');
            return $this->redirect($this->generateUrl('usuario_login'));

    }
    public function cambioclaveAction()
    {
        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);

        if($entity->getUser()->getPassword()==null){
            return $this->redirect($this->generateUrl('usuario_homepage'));
        }

        $entity = new Cambioclave();
        $form   = $this->createForm(new CambioclaveType(), $entity);

        return $this->render('UsuarioBundle:User:cambioclave.html.twig', array(
            'form' => $form->createView(),        ));
    }

    public function actualizacambioclaveAction(Request $request)
    {

        $entity = new Cambioclave();
        $form   = $this->createForm(new CambioclaveType(), $entity);
        $form->bind($request);
        
        if ($form->isValid() ) {

            //valido
            $datos=$request->request->all();
            $datos=$datos['cambioclave'];
            
            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);


            if(md5($datos['claveanterior'])!=$entity->getUser()->getPassword()){
                $this->get('session')->getFlashBag()->add('alert', 'Su clave actual no coincide.');
                return $this->render('UsuarioBundle:User:cambioclave.html.twig', array(
                'form' => $form->createView(),        ));
            }
            
     
            if(md5($datos['clavenueva'])!=md5($datos['claveconfirmacion'])){
                $this->get('session')->getFlashBag()->add('alert', 'Su clave nueva y de confirmación no coinciden.');
                return $this->render('UsuarioBundle:User:cambioclave.html.twig', array(
                'form' => $form->createView(),        ));
            }

            if(md5($datos['clavenueva'])==md5($datos['claveanterior'])){
                $this->get('session')->getFlashBag()->add('alert', 'Su clave nueva no debe ser igual a la anterior.');
                return $this->render('UsuarioBundle:User:cambioclave.html.twig', array(
                'form' => $form->createView(),        ));
            }
            //fin validar
            
            //desactivo operador
            $consulta = $em->createQuery('update UsuarioBundle:User u set u.password= :pass WHERE u.id = :iduser');
            $consulta->setParameter('iduser', $idusuario);
            $consulta->setParameter('pass', md5($datos['clavenueva']));
            $consulta->execute();
            
            $this->get('session')->getFlashBag()->add('notice', 'Su clave fue actualizada correctamente!!.');
            return $this->redirect($this->generateUrl('usuario_homepage'));
        }
        return $this->render('UsuarioBundle:User:cambioclave.html.twig', array(
            'form' => $form->createView(),        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        //$entity = $em->getRepository('UsuarioBundle:User')->find($id);

        
        $em = $this->getDoctrine()->getManager();
        $usuarios= $em->createQuery('SELECT p FROM UsuarioBundle:Perfil p
        JOIN p.user u
        WHERE u.id = :userid
        ');
        $usuarios->setParameter('userid', $id);
        $entity = $usuarios->getSingleResult();

        
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);
        
        $perfil = new Perfil();
        $form_perfil   = $this->createForm(new PerfilType(), $perfil);

        return $this->render('UsuarioBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            
            'perfil' => $perfil,
            'form_perfil'   => $form_perfil->createView(),
        ));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($request);
        
        $entity_perfil  = new Perfil();
        $form_perfil = $this->createForm(new PerfilType(), $entity_perfil);
        $form_perfil->bind($request);

        
        if ($form->isValid() && $form_perfil->isValid()) {
            
            //CODIFICO LA CONTRASEÑA
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);
        
            //AGREGO EL ID DEL USUARIO PARA EL PERFIL
            $entity_perfil->setUser($entity);
            
            //GUARDO LOS DATOS
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($entity_perfil);
            $em->flush();
            
            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return $this->render('UsuarioBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            
            'entity_perfil' => $entity_perfil,
            'form_perfil'   => $form_perfil->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:User')->find($id);
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($id);
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        $editFormPerfil = $this->createForm(new PerfilType(), $perfil);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'edit_form_perfil'   => $editFormPerfil->createView(),
            'delete_form' => $deleteForm->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $datos=$request->request->all();
        $datos=$datos['administracion_usuariobundle_usertype'];

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:User')->find($id);
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($id);
        
        $passviejo=$entity->getPassword();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->handleRequest($request);
        
        
        $usuarios= $em->createQuery('SELECT p FROM UsuarioBundle:Perfil p WHERE p.user = :userid');
        $usuarios->setParameter('userid', $id);
        $entity_perfil = $usuarios->getSingleResult();
        $editFormPerfil = $this->createForm(new PerfilType(), $entity_perfil);
        $editFormPerfil->bind($request);

        if ($editForm->isValid() && $editFormPerfil->isValid()) {
            //procesar archivo
            
            
            if($editFormPerfil['file']->getData()):

                $file=$editFormPerfil['file']->getData();
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $nombre=$file->getClientOriginalName();
                $nombreLargo=explode(".", $nombre);
                $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
                $extension = $nombreLargo[1];
                
                //validaciones
                    $extensiones=array('jpg','jpeg','JPG','JPEG',);
                    if (!in_array($extension,$extensiones)) {
                        $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                        return $this->render('UsuarioBundle:User:edit.html.twig', array(
                            'entity'      => $entity,
                            'edit_form'   => $editForm->createView(),
                            'edit_form_perfil'   => $editFormPerfil->createView(),
                            'delete_form' => $deleteForm->createView(),
                        ));
                    }
                //fin
                $escaleraNivel=  explode("-", $perfil->getNivelorganizacional()->getCodigo());
                $ruta=null;
                $cadenaNivel=null;
                for($i=0;$i < (count($escaleraNivel));$i++):
                
                    if($i==0)
                        $cadenaNivel = $escaleraNivel[$i];
                    else
                        $cadenaNivel .="-".$escaleraNivel[$i];
                    
                        $dql = "select x from UsuarioBundle:Nivelorganizacional x where x.codigo like :codigo";
                        $query = $em->createQuery($dql);
                        $query->setParameter('codigo',$cadenaNivel);
                        $nivel = $query->getResult();
                        $ruta .=str_replace(" ","-",strtoupper($this->quitarAcentos($nivel[0]->getDescripcion())))."/";
                endfor;
                
                $ruta='abc/';

                if($file->move('/var/www/uploads/personalTelesur/',$perfil->getCedula().'.jpg') )
                {
                    $perfil->setFoto('/uploads/personalTelesur/'.$perfil->getCedula().'.jpg');
                }
                
            endif;
            //fin procesar archivo
             if(!empty($datos['password'])){
                //CODIFICO LA CONTRASEÑA
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
            } else{

                $entity->setPassword($passviejo);
            }
            
            $em->persist($entity);
            $em->persist($entity_perfil);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Actualización exitosa!');

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

         $this->get('session')->getFlashBag()->add('alert', 'Alerta! Ha ocurrido un error en el formulario.');
        return $this->render('UsuarioBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'edit_form_perfil'   => $editFormPerfil->createView(),
            'delete_form' => $deleteForm->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:User')->find($id);
            
            $usuarios= $em->createQuery('SELECT p FROM UsuarioBundle:Perfil p WHERE p.user = :userid');
            $usuarios->setParameter('userid', $id);
            $entity_perfil = $usuarios->getSingleResult();

            if (!$entity_perfil) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity_perfil);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    

}
