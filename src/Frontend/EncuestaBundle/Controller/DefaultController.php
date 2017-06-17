<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Frontend\EncuestaBundle\Entity\Encuesta;
use Frontend\EncuestaBundle\Form\EncuestaType;

use Frontend\EncuestaBundle\Entity\Pregunta;
use Frontend\EncuestaBundle\Form\PreguntaType;

use Frontend\EncuestaBundle\Entity\Respuesta;
use Frontend\EncuestaBundle\Form\RespuestaType;

use Frontend\EncuestaBundle\Entity\Votos;
use Frontend\EncuestaBundle\Form\VotosType;

use Frontend\EncuestaBundle\Entity\Resultados;
use Frontend\EncuestaBundle\Form\ResultadosType;

use Frontend\EncuestaBundle\Entity\Usuariosegundos;

class DefaultController extends Controller
{
  /*
  *
  *
  *
  * FUNCION QUE RECARGA LA PREGUNTA ACTUAL CUANDO LA PAGINA ES ACTUALIZADA POR EL USUARIO
  *
  *
  *
  */
    public function ajax_recargapreguntaAction($idpregunta, $preguntas, $contador)
    {
        $em = $this->getDoctrine()->getManager();

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
    		if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
    		{
          throw new AccessDeniedException();
    		}

        //obtengo el array con las preguntas
        $preguntasid=explode(",",$preguntas);

        //obtengo el id de la siguiente pregunta
        $idsiguiente=$preguntasid[0];
        $contadorr = 0;
        foreach ($preguntasid as $var) {
          $contadorr=$contadorr+1;
        }

        //entra al if si existe una pregunta siguiente a mostrar
        if($contadorr > 1){
        if($idsiguiente != 'u'){
            if($preguntasid[1] != NULL){
              $preguntas=null;$cont=0;
              foreach ($preguntasid as $var) {
                if($cont!=0)
                    $preguntas .= $var.",";
                $cont++;
              }
              $preguntas=substr($preguntas,0,-1);
            }else {
              $preguntas = 'u';
            }
        }else{
            $preguntas = 'u';
        }
      }else{

          $preguntas='u';
        }


        //obtengo el id del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();

        //busco la pregunta de la encuesta
        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta = $pregunta[0];

        /*obtengo las respuestas de esta pregunta*/
        $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($pregunta->getId());

        foreach ($respuesta as $key)
        {
            $respuestas[$key->getId()] = array($key->getId() => $key->getRespuesta());
        }

        //creo el form de las respuestas
        $form = $this->createFormBuilder()
            ->add('respuesta', 'choice', array(
                                                 'choices'   => $respuestas,
                                                  'expanded' => true,
                                                  'multiple' => false
                                                )
                 )
            ->getForm();

        //envio a la vista
        return $this->render('EncuestaBundle:Votos:ajax_votartrivia.html.twig', array(
            'entity'      => $pregunta,
            'contador'    => $contador,
            'preguntas'   => $preguntas,
            'form'        => $form->createView(),
            'usuario'     => $idusuario
        ));

    }//fin de la funcion (ajax_recargapregunta)

/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/


    /* MAS DE UNA PREGUNTA
    *
    *
    *
    * FUNCION PARA GUARDAR VOTO Y CARGAR LA SIGUIENTE PREGUNTA SI EL CONTADOR LLEGA A 0
    *
    *
    *
    */
    public function ajax_votartriviaAction($idrespuesta, $idpregunta, $preguntas, $contador, $segundosusados)
    {
      $em = $this->getDoctrine()->getManager();

      //instancio mi servicio trivias
      $trivias = $this->get('trivias');

      //VERIFICAR SI EL USUARIO ESTA LOGUEADO
  		if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
  		{
        throw new AccessDeniedException();
  		}

      //obtengo el id del usuario conectado
      $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
      $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

      //busco la pregunta de la encuesta
      $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
      $pregunta = $pregunta[0];

      //busco la encuesta a la que pertenece esa pregunta
      $idencuesta = $pregunta->getIdencuesta()->getId();
      $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
      $encuesta = $encuesta[0];

      //verifico si la encuesta tiene preguntas, respuestas, está activa y si el usuario conectado ya voto en la encuesta
      $correcta = $trivias->verificarencuesta($idencuesta, $usuario);

      $correcta_0  = $correcta[0];
      $correcta_1  = $correcta[1];
      $correcta_2  = $correcta[2];
      $correcta_3  = $correcta[3];
      $correcta_4  = $correcta[4];
      $preguntas_usuario  = $correcta[5];

      //es cero cuando la encuesta no tiene preguntas asociadas
      if($correcta_0 == 0)
      {
        $alerta = true;
        $mensaje = "La encuesta no tiene preguntas asociadas";
      }elseif ($correcta_1 == 0) //es cero cuando la preguntas no tiene respuestas asociadas
      {
        $alerta = true;
        $mensaje = "La encuesta no tiene respuestas asociadas";
      }elseif ($correcta_2 == 0) //es cero cuando no existe al menos una respuesta correcta por cada pregunta
      {
        $alerta = true;
        $mensaje = "La encuesta no tiene respuestas correctas";
      }elseif ($correcta_3 == 1) //es uno cuando el usuario ya voto en la encuesta
      {
        $contador_votos = 0;
        if($preguntas_usuario != 0){
          foreach ($preguntas_usuario as $key) {
            if($key->getId()==$idpregunta){
              $contador_votos = $contador_votos ++;
            }
          }
          if($contador_votos > 0)
          {
            $alerta = true;
            $mensaje = "Usted ya voto en esta encuesta";
          }else {
            $alerta = false;
          }
        }else {
          $alerta= false;
        }
      }elseif ($correcta_4 == 2) //es dos cuando la encuesta no está activa
      {
        $alerta = true;
        $mensaje = "La encuesta no se encuentra activa";
      }else {
        $alerta=false;
      }

      //entra al if si no hay alertas
      if($alerta==false)
      {
              //obtengo el array con las preguntas
              $preguntasid=explode(",",$preguntas);

              //obtengo el id de la siguiente pregunta
              $idsiguiente=$preguntasid[0];
              $contadorr = 0;
              foreach ($preguntasid as $var) {
                $contadorr=$contadorr+1;
              }

              //entra al if si existe una pregunta siguiente a mostrar
              if($contadorr > 1){
              if($idsiguiente != 'u'){
                  if($preguntasid[1] != NULL){
                    $preguntas=null;$cont=0;
                    foreach ($preguntasid as $var) {
                      if($cont!=0)
                          $preguntas .= $var.",";
                      $cont++;
                    }
                    $preguntas=substr($preguntas,0,-1);
                  }else {
                    $preguntas = 'u';
                  }
              }else{
                  $preguntas = 'u';
              }
            }else{

                $preguntas='u';
              }


              $usuariosegundos1 = new Usuariosegundos();
              $segusados  = $trivias->actualizasegundos($usuario, $segundosusados, $encuesta, $usuariosegundos1);

              $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));

              //entra al if si el usuario seleccionó una respuesta
              if($idrespuesta != 'N')
              {
                //obtengo las respuestas
                $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findById($idrespuesta);
                $respuesta = $respuesta[0];

                $idpregunta = $respuesta->getIdpregunta()->getId();

                $pregunta1 = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
                $pregunta1 = $pregunta1[0];

                //verifico si ya existe el voto del usuario a esta pregunta
                $dql = "select count(v.idencuesta) from EncuestaBundle:Votos v where v.idencuesta=:encuesta
                and v.idpregunta=:pregunta and v.idusuario=:usuario";


               $consulta = $em->createQuery($dql)->setParameters(array( 'encuesta'  => $encuesta->getId(),
                                                                        'pregunta'  => $idpregunta,
                                                                        'usuario'   => $usuario->getId()
                                                                      ));
               $existente = $consulta->getResult();


               //si no existe entra al if
               if($existente[0][1] == 0)
                {
                  //inserto el voto
                  $entity = new Votos();
                  $entity->setIdencuesta($encuesta);
                  $entity->setIdpregunta($pregunta1);
                  $entity->setIdrespuesta($respuesta);
                  $entity->setIdsuario($usuario);

                  $em->persist($entity);
                  $em->flush();

                  //si $idsiguiente = u es la ultima
                  if($idsiguiente == 'u')
                  {
                    $puntaje  = $trivias->puntaje($usuario, $encuesta);

                    //traigo los resultados
                    $resultado  = $trivias->traerresultado($usuario, $encuesta);

                    $result  = count($resultado);

                    /* SE VERIFICA SI YA EXISTE UN RESULTADO ANTERIOR DE ESTE USUARIO A ESTA ENCUESTA */
                    if($result == 0)
                    {
                      //inserto el Resultados
                      $entity1 = new Resultados();

                      $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));

                      $entity1->setFecha($hoy);

                      $entity1->setIdencuesta($encuesta);
                      $entity1->setIdusuario($usuario);
                      $entity1->setPuntos($puntaje);

                      $em->persist($entity1);
                      $em->flush();

                      //http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/2/resultados
                      $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                      $html2 = "/resultados'>aqui<a/>";
                      $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                      echo $html;die;
                      return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                    }else {

                      $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                      $html2 = "/resultados'>aqui<a/>";
                      $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                      echo $html;die;
                      return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta>getId())));
                    }
                  }else {//else preguntas = u
                    //obtengo la pregunta siguiente
                    $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idsiguiente);
                    $pregunta = $pregunta[0];

                    //obtengo las respuestas de esta pregunta
                    $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($pregunta->getId());

                    foreach ($respuesta as $key)
                    {
                        $respuestas[$key->getId()] = array($key->getId() => $key->getRespuesta());
                    }

                    //creo el form de las respuestas
                    $form = $this->createFormBuilder()
                        ->add('respuesta', 'choice', array(
                                                             'choices'   => $respuestas,
                                                              'expanded' => true,
                                                              'multiple' => false
                                                            )
                             )
                        ->getForm();
                        //envio a la vista
                        return $this->render('EncuestaBundle:Votos:ajax_votartrivia.html.twig', array(
                            'entity'      => $pregunta,
                            'contador'    => count($preguntasid)-1,
                            'preguntas'   => $preguntas,
                            'form'        => $form->createView(),
                            'usuario'     => $idusuario
                        ));
                  }//fin if si preguntas es == u

                }else{ //ya existe el voto
                  //obtengo los puntos que obtuvo la persona en la encuesta

                  $puntaje  = $trivias->puntaje($usuario, $encuesta);

                  //traigo los resultados
                  $resultado  = $trivias->traerresultado($usuario, $encuesta);

                  $result  = count($resultado);

                  /* SE VERIFICA SI YA EXISTE UN RESULTADO ANTERIOR DE ESTE USUARIO A ESTA ENCUESTA */
                  if($result == 0)
                  {
                    //inserto el Resultados
                    $entity1 = new Resultados();

                    $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));

                    $entity1->setFecha($hoy);

                    $entity1->setIdencuesta($encuesta);
                    $entity1->setIdusuario($usuario);
                    $entity1->setPuntos($puntaje);
                    $em->persist($entity1);
                    $em->flush();


                    $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                    $html2 = "/resultados'>aqui<a/>";
                    $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                    echo $html;die;
                  return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));

                  }else {

                    $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                    $html2 = "/resultados'>aqui<a/>";
                    $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                    echo $html;die;
                    return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                  }
                }
              }else{ /* el usuario no respondió, idrespuesta igual a N*/

                /* entra al if si la pregunta actual es la ultima */
                if($idsiguiente != 'u')
                {
                  //obtengo la pregunta siguiente
                  $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idsiguiente);
                  $pregunta = $pregunta[0];

                  //obtengo las respuestas de esta pregunta
                  $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($pregunta->getId());

                  foreach ($respuesta as $key)
                  {
                      $respuestas[$key->getId()] = array($key->getId() => $key->getRespuesta());
                  }

                  //creo el form de las respuestas
                  $form = $this->createFormBuilder()
                      ->add('respuesta', 'choice', array(
                                                           'choices'   => $respuestas,
                                                            'expanded' => true,
                                                            'multiple' => false
                                                          )
                           )
                      ->getForm();
                      //envio a la vista
                      return $this->render('EncuestaBundle:Votos:ajax_votartrivia.html.twig', array(
                          'entity'      => $pregunta,
                          'contador'    => count($preguntasid)-1,
                          'preguntas'   => $preguntas,
                          'form'        => $form->createView(),
                          'usuario'     => $idusuario
                      ));
                }else { /* entra al else si idsiguiente  == 'u'*/
                  $puntaje  = $trivias->puntaje($usuario, $encuesta);

                  //traigo los resultados
                  $resultado  = $trivias->traerresultado($usuario, $encuesta);

                  $result  = count($resultado);

                  /* SE VERIFICA SI YA EXISTE UN RESULTADO ANTERIOR DE ESTE USUARIO A ESTA ENCUESTA */
                  if($result == 0)
                  {
                    //inserto el Resultados
                    $entity1 = new Resultados();

                    $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));

                    $entity1->setFecha($hoy);

                    $entity1->setIdencuesta($encuesta);
                    $entity1->setIdusuario($usuario);
                    $entity1->setPuntos($puntaje);
                    $em->persist($entity1);
                    $em->flush();

                    $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                    $html2 = "/resultados'>aqui<a/>";
                    $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                    echo $html;die;
                    return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));

                  }else {

                    $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                    $html2 = "/resultados'>aqui<a/>";
                    $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                    echo $html;die;
                    return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                  }/* fin if $result= 0, resultados asociados */
                }/*fin if idsiguiente  == 'u'*/
              }/* fin if usuario no respondio*/
          }else{

            //envio a la vista de activas si hay alertas
              $activaa = $trivias->activa($usuario);

              $encuestas  = $activaa[0];
              $activa     = $activaa[1];
              $contador   = $activaa[2];
              $puntos     = $activaa[3];
              $voto       = $activaa[4];

              $this->get('session')->getFlashBag()->add('alert', $mensaje);
              return $this->render('EncuestaBundle:Encuesta:activa.html.twig', array(
                  'entities'  => $encuestas,
                  'activa'    => $activa,
                  'contador'  => $contador,
                  'puntos'    => $puntos,
                  'voto'      => $voto
              ));
        }
    }//fin de la funcion (ajax_votartrivia)

/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************************************/


    /* GUARDAR VOTOS
    *
    *
    *
    * FUNCION PARA GUARDAR ULTIMO VOTO Y OBTENER PUNTAJE SI CONTADOR LLEGA A 0
    *
    *
    *
    */
    public function ajax_guardarvotosAction($idrespuesta, $idpregunta, $segundosusados)
    {
        $em = $this->getDoctrine()->getManager();

        $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
    		if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
    		{
          throw new AccessDeniedException();
    		}
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta = $pregunta[0];
        $idencuesta = $pregunta->getIdencuesta()->getId();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $trivias = $this->get('trivias');

        //verifico si la encuesta tiene preguntas, respuestas, está activa y si el usuario conectado ya voto en la encuesta
        $correcta = $trivias->verificarencuesta($idencuesta, $usuario);

        $correcta_0  = $correcta[0];
        $correcta_1  = $correcta[1];
        $correcta_2  = $correcta[2];
        $correcta_3  = $correcta[3];
        $correcta_4  = $correcta[4];
        $preguntas_usuario  = $correcta[5];

        //es cero cuando la encuesta no tiene preguntas asociadas
        if($correcta_0 == 0)
        {
          $alerta = true;
          $mensaje = "La encuesta no tiene preguntas asociadas";
        }elseif ($correcta_1 == 0) //es cero cuando la preguntas no tiene respuestas asociadas
        {
          $alerta = true;
          $mensaje = "La encuesta no tiene respuestas asociadas";
        }elseif ($correcta_2 == 0) //es cero cuando no existe al menos una respuesta correcta por cada pregunta
        {
          $alerta = true;
          $mensaje = "La encuesta no tiene respuestas correctas";
        }elseif ($correcta_3 == 1) //es uno cuando el usuario ya voto en la encuesta
        {
          $contador_votos = 0;
          if($preguntas_usuario != 0){
            foreach ($preguntas_usuario as $key) {
              if($key->getId()==$idpregunta){
                $contador_votos = $contador_votos ++;
              }
            }
            if($contador_votos > 0)
            {
              $alerta = true;
              $mensaje = "Usted ya voto en esta encuesta";
            }else {
              $alerta = false;
            }
          }else {
            $alerta = false;
          }
        }elseif ($correcta_4 == 2) //es dos cuando la encuesta no está activa
        {
          $alerta = true;
          $mensaje = "La encuesta no se encuentra activa";
        }else {
          $alerta=false;
        }

        if($alerta==false)
        {

          $usuariosegundos1 = new Usuariosegundos();
          $segsusados  = $trivias->actualizasegundos($usuario, $segundosusados, $encuesta, $usuariosegundos1);
                    if($idrespuesta != 'N')
                    {

                      $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findById($idrespuesta);
                      $respuesta = $respuesta[0];

                      $dql = "select count(v.idencuesta) from EncuestaBundle:Votos v where v.idencuesta=:encuesta
                      and v.idpregunta=:pregunta and v.idusuario=:usuario";


                     $consulta = $em->createQuery($dql)->setParameters(array( 'encuesta'  => $encuesta->getId(),
                                                                              'pregunta'  => $pregunta->getId(),
                                                                              'usuario'   => $usuario->getId()
                                                                            ));
                     $existente = $consulta->getResult();

                     if($existente[0][1] == 0)
                      {

                        $entity = new Votos();
                        $entity->setIdencuesta($encuesta);
                        $entity->setIdpregunta($pregunta);
                        $entity->setIdrespuesta($respuesta);
                        $entity->setIdsuario($usuario);

                        $em->persist($entity);
                        $em->flush();

                        $puntaje  = $trivias->puntaje($usuario, $encuesta);

                        $resultado  = $trivias->traerresultado($usuario, $encuesta);

                        $result  = count($resultado);

                        if($result == 0){


                          $entity1 = new Resultados();

                          $entity1->setFecha($hoy);
                          $entity1->setIdencuesta($encuesta);
                          $entity1->setIdusuario($usuario);
                          $entity1->setPuntos($puntaje);

                          $em->persist($entity1);
                          $em->flush();

                          $puntaje1  = $trivias->puntaje($usuario, $encuesta);
                          $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                          $html2 = "/resultados'>aqui<a/>";
                          $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                          echo $html;die;
                          return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));

                        }else {
                          $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                          $html2 = "/resultados'>aqui<a/>";
                          $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                          echo $html;die;
                          return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                        }
                      }else{ //si existe el voto

                        $resultado  = $trivias->traerresultado($usuario, $encuesta);
                        $puntaje  = $trivias->puntaje($usuario, $encuesta);

                        $result  = count($resultado);

                        if($result == 0){


                            $entity1 = new Resultados();

                            $entity1->setFecha($hoy);
                            $entity1->setIdencuesta($encuesta);
                            $entity1->setIdusuario($usuario);
                            $entity1->setPuntos($puntaje);

                            $em->persist($entity1);
                            $em->flush();

                            $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                            $html2 = "/resultados'>aqui<a/>";
                            $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                            echo $html;die;
                            return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));

                        }else{

                          $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                          $html2 = "/resultados'>aqui<a/>";
                          $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                          echo $html;die;
                            return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                        }
                      }

                    }else{ //si el usuario no respondio

                      $puntaje  = $trivias->puntaje($usuario, $encuesta);
                      $resultado  = $trivias->traerresultado($usuario, $encuesta);

                      $result  = count($resultado);

                      if($result == 0){

                        $entity1 = new Resultados();

                        $entity1->setFecha($hoy);
                        $entity1->setIdencuesta($encuesta);
                        $entity1->setIdusuario($usuario);
                        $entity1->setPuntos($puntaje);

                        $em->persist($entity1);
                        $em->flush();

                        $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                        $html2 = "/resultados'>aqui<a/>";
                        $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                        echo $html;die;
                        return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));

                      }else {

                        $html1 = "<br><br><br><br>Calculando Resultados <br> Para verlos directamente presione <a href='http://aplicaciones.telesurtv.net/sait/web/app_dev.php/encuesta/resultados/";
                        $html2 = "/resultados'>aqui<a/>";
                        $html = $html1.$encuesta->getId()."/".$idusuario.$html2;
                        echo $html;die;
                        return $this->redirect($this->generateUrl('mostrar_resultado', array('idencuesta' => $encuesta->getId())));
                      }
                    }
          }else {

                  $activaa = $trivias->activa($usuario);

                    $encuestas  = $activaa[0];
                    $activa     = $activaa[1];
                    $contador   = $activaa[2];
                    $puntos     = $activaa[3];
                    $voto       = $activaa[4];

                    $this->get('session')->getFlashBag()->add('alert', $mensaje);
                    return $this->render('EncuestaBundle:Encuesta:activa.html.twig', array(
                        'entities'  => $encuestas,
                        'activa'    => $activa,
                        'contador'  => $contador,
                        'puntos'    => $puntos,
                        'voto'      => $voto
                    ));
          }

    }/*fin de la funcion ajax_guardarvotos*/

}//fin de clase DefaultController
