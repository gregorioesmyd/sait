<?php

namespace Frontend\EncuestaBundle\Servicios;


class trivias
{
    function __construct($em) {
        $this->em = $em;
    }

    /* Redondea el valor enviado  */
    public function redondear($valor){
      $resultado = round($valor, 0, PHP_ROUND_HALF_UP);
      return $resultado;
    }

    public function actualizasegundos($usuario, $segundos, $encuesta, $usuariosegundos1)
    {
      $em=$this->em;


      $dql = "select us from EncuestaBundle:Usuariosegundos us where us.idusuario=:usuario and us.idencuesta=:encuesta";
      $consulta = $em->createQuery($dql)->setParameters(array(
                                                               'usuario'  => $usuario->getId(),
                                                               'encuesta' => $encuesta->getId(),
                                                             ));
      $usuariosegundos = $consulta->getResult();

      $result  = count($usuariosegundos);

      if($result == 0)
      {
        $entity = $usuariosegundos1;
        $entity->setIdencuesta($encuesta);
        $entity->setIdsuario($usuario);
        if($segundos == 'null' or $segundos== null or $segundos == ' '){
          $entity->setSegundos(0);
        }else {
          $entity->setSegundos($segundos);
        }
      }else {
        $entity = $usuariosegundos[0];
        $entity->setSegundos($segundos);
      }
      $em->persist($entity);
      $em->flush();

      return $entity;
    }



    //calcula si la encuesta esta activa
    public function esactiva($hoy,$fechainicio, $horainicio, $fechafin, $horafin){

      $timestamp_inicio = mktime($horainicio[0],$horainicio[1],$horainicio[2],$fechainicio[0],$fechainicio[1],$fechainicio[2]);
      $timestamp_fin = mktime($horafin[0],$horafin[1],$horafin[2],$fechafin[0],$fechafin[1],$fechafin[2]);
      $timestamp_hoy = mktime($hoy[3],$hoy[4],$hoy[5],$hoy[0],$hoy[1],$hoy[2]);

      if($timestamp_inicio <= $timestamp_hoy && $timestamp_fin >= $timestamp_hoy)
      {
        $activa = 1;
      }else{
        $activa = 2;
      }
      return $activa;
    }

    //se llama desde el index de la actividad
    public function activa($usuario){
      $em=$this->em;

      $dql = "select e from EncuestaBundle:Encuesta e where e.tipoencuesta=1";
      $consulta = $em->createQuery($dql);
      $encuestas = $consulta->getResult();

      $contador = 0;
      if(!empty($encuestas)){

            foreach ($encuestas as $key) {
                $verificarencuesta= $this->verificarencuesta($key->getId(), $usuario);

                if($verificarencuesta[4]== 1)
                {
                  if($verificarencuesta[0] == 0)
                  {
                    $voto[$key->getId()]    = 0;
                    $puntos[$key->getId()]  = 0;
                    $activa[$key->getId()]  = 2;

                  }else{
                    if($verificarencuesta[1] == 0){

                      $voto[$key->getId()]    = 0;
                      $puntos[$key->getId()]  = 0;
                      $activa[$key->getId()]  = 2;
                    }else {
                      if($verificarencuesta[2] == 0){

                        $voto[$key->getId()]    = 0;
                        $puntos[$key->getId()]  = 0;
                        $activa[$key->getId()]  = 2;
                      }else {
                        if($verificarencuesta[3] == 1){
                          $voto[$key->getId()]    = 1;
                          $activa[$key->getId()]  = 1;
                          $contador++;
                          $punto=$this->puntaje($usuario, $key);
                          $puntos[$key->getId()] = round($punto, 0, PHP_ROUND_HALF_UP);
                        }else {
                          $voto[$key->getId()] = 0;
                          $activa[$key->getId()]=1;
                          $puntos[$key->getId()] = 0;
                          $contador++;
                        }
                      }
                    }
                  }
                }else{
                  $voto[$key->getId()]    = 0;
                  $puntos[$key->getId()]  = 0;
                  $activa[$key->getId()]  = 2;
                }

            }

      }else {
          $encuestas = 0;
          $activa = 0;
          $puntos = 0;
          $voto = 0;
      }


      return array($encuestas, $activa, $contador, $puntos, $voto);

    }

    //calcula los dias entre dos fechas
    public function dias($fecha1, $fecha2, $hora1){

            $timestamp2 = mktime($fecha2[3],$fecha2[4],$fecha2[5],$fecha2[0],$fecha2[1],$fecha2[2]);
            $timestamp1 = mktime($hora1[0],$hora1[1],$hora1[2],$fecha1[0],$fecha1[1],$fecha1[2]);
            $segundos_diferencia = $timestamp1 - $timestamp2;
            return $segundos_diferencia;
    }

    public function comenzotermino()
    {
      $em=$this->em;
      $entities = $em->getRepository('EncuestaBundle:Encuesta')->findAll();

      $hoy = date('m-d-Y-H-i-s');
      $fecha2 = explode("-", $hoy);

      if(!empty($entities)){
          foreach ($entities as $key) {
            $fecha_1 = $key->getFechainicioencuesta();
            $fecha_uno = $fecha_1->format('m-d-Y');

            $hora_1 = $key->getHorainicio();
            $hora_uno = $hora_1->format('H-i-s');

            $fecha1 = explode("-", $fecha_uno);
            $hora1 = explode("-", $hora_uno);

            $segundos_diferencia = $this->dias($fecha1,$fecha2, $hora1);

            if( $segundos_diferencia <= 0){
              $comenzo[$key->getId()] = 1;
            }else {

              $comenzo[$key->getId()] = 0;
            }

            $fecha_1 = $key->getFechafinencuesta();
            $fecha_uno = $fecha_1->format('m-d-Y');

            $hora_1 = $key->getHorafin();
            $hora_uno = $hora_1->format('H-i-s');

            $fecha1 = explode("-", $fecha_uno);
            $hora1 = explode("-", $hora_uno);

            $segundos_diferencia = $this->dias($fecha1,$fecha2, $hora1);

            if( $segundos_diferencia <= 0){
              $termino[$key->getId()] = 1;
            }else {

              $termino[$key->getId()] = 0;
            }
        }
    }else {
      $comenzo = 0;
      $termino = 0;
    }
    return array($entities, $comenzo, $termino);
  }
  public function puntaje($usuario,$encuesta)
  {

      $em=$this->em;
      $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findBy(
                                      array( 'idencuesta' => $encuesta->getId()),
                                      array( 'id' => 'DESC')
                                    );

      $puntaje = 0;

      foreach ($pregunta as $key) {
        $dql1= "select r.id from EncuestaBundle:Respuesta r where r.idpregunta=:pregunta and r.correcta='t'";
        $consulta1 = $em->createQuery($dql1)->setParameters(array(
                                                                 'pregunta'  => $key->getId()
                                                               ));
        $respuesta_correcta = $consulta1->getResult();

        $dql2= "select r.id from EncuestaBundle:Votos v, EncuestaBundle:Respuesta r
        where v.idrespuesta = r.id and v.idencuesta=:encuesta  and v.idpregunta=:pregunta and v.idusuario=:usuario";
        $consulta2 = $em->createQuery($dql2)->setParameters(array(
                                                                  'encuesta'  => $encuesta,
                                                                  'pregunta'  => $key->getId(),
                                                                  'usuario'   => $usuario->getId()
                                                               ));
        $respuesta_usuario = $consulta2->getResult();

        if($respuesta_usuario != NULL)
        {
          if($respuesta_correcta[0]['id'] == $respuesta_usuario[0]['id'])
          {
            $puntos = $key->getIdencuesta()->getPuntospregunta();
            $puntaje = $puntaje + $puntos;
          }
        }
    }

  //$puntaje =  round($puntaje, 0, PHP_ROUND_HALF_UP);

  return $puntaje;
}

  public function voto($usuario,$encuesta){
    $em=$this->em;

    $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findBy(
                                    array( 'idencuesta' => $encuesta->getId()),
                                    array( 'id' => 'DESC')
                                  );

    $puntaje = 0;

    foreach ($pregunta as $key) {
      $dql2= "select v.id from EncuestaBundle:Votos v
      where v.idencuesta=:encuesta v.idusuario=:usuario";
      $consulta2 = $em->createQuery($dql2)->setParameters(array(
                                                                'encuesta'  => $encuesta,
                                                                'usuario'   => $usuario->getId()
                                                             ));
      $respuesta_usuario = $consulta2->getResult();

    }
  }



  public function traerresultado($usuario,$encuesta)
  {
      $em=$this->em;
      $resultado = $em->getRepository('EncuestaBundle:Resultados')->findBy(
                                      array( 'idencuesta' => $encuesta->getId(),
                                             'idusuario'  => $usuario->getId()                                    ),
                                      array( 'id' => 'DESC')
                                    );
      return $resultado;
  }


  public function verificarencuesta($idencuesta, $usuario)
  {
      $em=$this->em;

      $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
      $encuesta = $encuesta[0];
      /* SE VERIFICA SI LA ENCUESTA TIENE PREGUNTAS ASOCIADAS */
      $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findByIdencuesta($idencuesta);

      if($pregunta == NULL)
      {
          $pregasoci=0;
          $respasoci=0;
          $respcor=0;
      }else {
          $pregasoci=1;

        /* SE VERIFICA SI TODAS LAS PREGUNTAS DE LA ENCUESTA TIENEN RESPUESTAS ASOCIADAS */
        foreach ($pregunta as $key)
        {
            $contador = 0;
            $respuesta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($key->getId());

            if($respuesta == NULL)
            {
                $respasoci=0;
                $respcor=0;
            }else
            {
                $respasoci=1;

                /* SE VERIFICA SI LAS RESPUESTAS TIENEN RESPUESTAS CORRECTAS */
                foreach ($respuesta as $valor)
                {
                    if($valor->getCorrecta()==true)
                    {
                      $contador++;
                    }
                }
                if($contador == 0)
                {
                    $respcor=0;
                }else
                {
                    $respcor=1;
                }
            }
        }
      }

      /* SE VERIFICA SI EL USUARIO YA VOTO EN ESTA ENCUESTA */
      $resultado= $this->traerresultado($usuario,$encuesta);

      $result=count($resultado);

      $preguntas_usuario = null;
      if($result != 0){
        $usuariovoto=1;
      }else {
        /* Se verifica si el usuario tiene votos asociados */
        $dql = "select v from EncuestaBundle:Votos v where v.idencuesta=:encuesta and v.idusuario=:usuario";

        $consulta = $em->createQuery($dql)->setParameters(array( 'encuesta'  => $idencuesta,
                                                                  'usuario'   => $usuario->getId()
                                                                ));
        $puntos[$encuesta->getId()] = $consulta->getResult();


        if(!empty($puntos[$encuesta->getId()])){
          $preguntas_usuario= $puntos[$encuesta->getId()];
          $usuariovoto = 1;
        }else {
          $preguntas_usuario == 0;
           $usuariovoto=0;
        }
      }

      $today = date('m-d-Y-H-i-s');
      $hoy = explode("-", $today);

      /* Se verifica si la encuesta esta activa*/
      //
      $fecha_ini = $encuesta->getFechainicioencuesta();
      $fecha_inicio = $fecha_ini->format('m-d-Y');

      $hora_ini = $encuesta->getHorainicio();
      $hora_inicio = $hora_ini->format('H-i-s');

      $fechainicio = explode("-", $fecha_inicio);
      $horainicio = explode("-", $hora_inicio);

      //
      $fecha_fin = $encuesta->getFechafinencuesta();
      $fechafin = $fecha_fin->format('m-d-Y');

      $hora_fin = $encuesta->getHorafin();
      $horafin = $hora_fin->format('H-i-s');

      $fechafin = explode("-", $fechafin);
      $horafin = explode("-", $horafin);

      $activa= $this->esactiva($hoy,$fechainicio, $horainicio, $fechafin, $horafin);

      return array($pregasoci,$respasoci,$respcor,$usuariovoto,$activa, $preguntas_usuario);
    }
}
