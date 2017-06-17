<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Frontend\EncuestaBundle\Entity\Votos;
use Frontend\EncuestaBundle\Form\VotosType;

use Frontend\EncuestaBundle\Entity\Pregunta;
use Frontend\EncuestaBundle\Form\PreguntaType;

use Frontend\EncuestaBundle\Entity\Encuesta;
use Frontend\EncuestaBundle\Form\EncuestaType;

use Frontend\EncuestaBundle\Entity\Respuesta;
use Frontend\EncuestaBundle\Form\RespuestaType;

use Frontend\EncuestaBundle\Entity\Resultados;
use Frontend\EncuestaBundle\Form\ResultadosType;

/**
 * Votos controller.
 *
 */
class VotosController extends Controller
{

    /**
     * Lists all Votos entities.
     *
     */
    public function graficoAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $preguntas = $em->getRepository('EncuestaBundle:Pregunta')->findBy(
                                        array( 'idencuesta' => $encuesta->getId()),
                                        array( 'id' => 'ASC')
                                        );

        foreach ($preguntas as $key)
        {
          $grafico[$key->getId()]="";
          $respuestas[$key->getId()] = $em->getRepository('EncuestaBundle:Respuesta')->findBy(
                                        array( 'idpregunta' => $key->getId()),
                                        array( 'id' => 'ASC')
          );

          foreach ($respuestas[$key->getId()] as $valor )
          {
            $voto = $em->getRepository('EncuestaBundle:Votos')->findByIdrespuesta($valor->getId());

            $votos[$key->getId()][$valor->getId()] = 0;
            foreach ($voto as $clave => $value)
            {
                $votos[$key->getId()][$valor->getId()] ++;
            }
            $grafico[$key->getId()] .="['".$valor->getRespuesta()."',".$votos[$key->getId()][$valor->getId()]."],";
          }
        }

        $grafico[$key->getId()] = substr($grafico[$key->getId()], 0, -1);



        return $this->render('EncuestaBundle:Votos:index.html.twig', array(
            'encuesta'    => $encuesta,
            'preguntas'   => $preguntas,
            'respuestas'  => $respuestas,
            'datosgrafico'=> $grafico,
            'votos'       => $votos,
        ));
    }

    /**
     * Votar entity.
     *
     */
    public function votartriviaAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
    		if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
    		{
          throw new AccessDeniedException();
    		}

        $trivias = $this->get('trivias');

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->find($idencuesta);

        $correcta = $trivias->verificarencuesta($idencuesta, $usuario);

        $correcta_0  = $correcta[0];
        $correcta_1  = $correcta[1];
        $correcta_2  = $correcta[2];
        $correcta_3  = $correcta[3];
        $correcta_4  = $correcta[4];

        if($correcta_0 == 0)
        {
          $alerta = true;
          $mensaje = "La encuesta no tiene preguntas asociadas";
        }elseif ($correcta_1 == 0) {
          $alerta = true;
          $mensaje = "La encuesta no tiene respuestas asociadas";
        }elseif ($correcta_2 == 0) {
          $alerta = true;
          $mensaje = "La encuesta no tiene respuestas correctas";
        }elseif ($correcta_3 == 1) {
          $alerta = true;
          $mensaje = "Usted ya voto en esta encuesta";
        }elseif ($correcta_4 == 2) {
          $alerta = true;
          $mensaje = "La encuesta no se encuentra activa";
        }else {
          $alerta=false;
        }

        if($alerta==false)
        {
              $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findByIdencuesta($idencuesta);
              $contador = 0;
              foreach ($pregunta as $key) {
                $contador ++;
              }

              $numeropregunta = $encuesta->getNumeropregunta();
              $preguntaid=array();
              if($numeropregunta>=$contador)
              {
                $i=0;
                foreach ($pregunta as $value) {
                  $preguntaid[$i] = $value->getId();
                  $i++;
                }
              }else{
                $contador = $numeropregunta;
                foreach ($pregunta as $value) {
                  $entrada[$value->getId()] = $value->getId();
                }
                $preguntaid = array_rand($entrada, $numeropregunta);
              }
              $pregunta0 = $preguntaid[0];
              $pregunta_0 = $em->getRepository('EncuestaBundle:Pregunta')->findById($pregunta0);
              //unset($preguntaid[0]);

              $preguntas = NULL;
              foreach ($preguntaid as $valor)
              {
                $preguntas .= $valor.",";
              }

              $preguntas = substr($preguntas, 0, -1);

                //envio a la vista
                return $this->render('EncuestaBundle:Votos:votartrivia.html.twig', array(
                    'entity'  =>  $pregunta_0[0],
                    'contador'    => $contador,
                    'preguntas'  => $preguntas,
                    'usuario'   => $idusuario
                ));
        }else{

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
    }

    /**
     * Votar entity.
     *
     */
    public function votarencuestaAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();
        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->find($idencuesta);

        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findByIdencuesta($idencuesta);
        $pregunta = $pregunta[0];

        if($pregunta->getIdencuesta()->getTipoencuesta()->getId()== 1)
        {

          $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($pregunta->getId());


          foreach ($respuesta as $key)
          {
              $respuestas[$key->getId()]=$key->getRespuesta();
          }

          $form = $this->createFormBuilder()
              ->add('respuesta', 'choice', array(
                                                    'choices'   => $respuestas,
                                                  )
                   )
              ->getForm();

          //envio a la vista
          return $this->render('EncuestaBundle:Votos:votar.html.twig', array(
              'entity' => $pregunta,
              'form'   => $form->createView(),
          ));
        }else {

          $trivias_activas = $this->get('trivias');

          $activaa = $trivias_activas->activa($usuario);

          $encuestas  = $activaa[0];
          $activa     = $activaa[1];
          $contador   = $activaa[2];
          $puntos     = $activaa[3];
          $voto       = $activaa[4];

          $this->get('session')->getFlashBag()->add('alert', 'No existen respuestas cargadas en este momento, ingrese mas tarde');

          return $this->render('EncuestaBundle:Encuesta:activa.html.twig', array(
              'entities'  => $encuestas,
              'activa'    => $activa,
              'contador'  => $contador,
              'puntos'    => $puntos,
              'voto'      => $voto
          ));
        }
    }










}
