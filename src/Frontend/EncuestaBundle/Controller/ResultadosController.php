<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EncuestaBundle\Entity\Resultados;
use Frontend\EncuestaBundle\Form\ResultadosType;

use Frontend\EncuestaBundle\Entity\Encuesta;
use Frontend\EncuestaBundle\Form\EncuestaType;

/**
 * Resultados controller.
 *
 */
class ResultadosController extends Controller
{

  public function ultimaAction()
  {
    return $this->render('EncuestaBundle:Default:ultima.html.twig');
  }


    /**
    *
    * Mostrar resultados
    *
    *
    */
    public function mostrar_resultadoAction($idencuesta, $idusuario)
    {
      $em = $this->getDoctrine()->getManager();

      $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

      $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
      $encuesta = $encuesta[0];

      $trivias = $this->get('trivias');
      $puntaje  = $trivias->puntaje($usuario, $encuesta);


      $resultado  = $trivias->traerresultado($usuario, $encuesta);
      $resultado = $resultado[0];

      $result  = count($resultado);

      $hoy = date_create_from_format('Y-m-d', \date("Y-m-d"));
      if($result == 0){
        $resultado = new Resultados();

        $resultado->setFecha($hoy);
        $resultado->setIdencuesta($encuesta);
        $resultado->setIdusuario($usuario);
        $resultado->setPuntos($puntaje);

        $em->persist($resultado);
        $em->flush();

      }else{

        if($resultado->getPuntos() == '' or $resultado->getPuntos() == null or $resultado->getPuntos() == '0.00' or $resultado->getPuntos() == '0,00'){
            $resultado->setPuntos($puntaje);
            $em->persist($resultado);
            $em->flush();
        }
      }


      $dql = "select us from EncuestaBundle:Usuariosegundos us where us.idusuario=:usuario and us.idencuesta=:encuesta";
      $consulta = $em->createQuery($dql)->setParameters(array(
                                                               'usuario'  => $usuario->getId(),
                                                               'encuesta' => $encuesta->getId(),
                                                             ));
      $usuariosegundos = $consulta->getResult();
      $usuariosegundos = $usuariosegundos[0];

      $puntos_r  = $trivias->redondear($resultado->getPuntos());

      return $this->render('EncuestaBundle:Resultados:ajax_resultado.html.twig', array(
          'hoy'          => $resultado->getFecha(),
          'puntaje'      => $puntos_r,
          'tiempo'       => $usuariosegundos->getSegundos(),
      ));
    }


    /**
     * Lists all Resultados entities.
     *
     */
    public function graficoAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findBy(
                                        array( 'id' => $idencuesta),
                                        array( 'id' => 'DESC')
                                      );
        $encuesta = $encuesta[0];

        $dql = "select p.primerNombre, p.segundoNombre,p.primerApellido, p.segundoApellido, r.puntos, us.segundos, n.descripcion from EncuestaBundle:Usuariosegundos us, EncuestaBundle:Resultados r,
        UsuarioBundle:Perfil p, UsuarioBundle:Nivelorganizacional n
        where r.idencuesta =:encuesta
        and us.idusuario = r.idusuario
        and p.user = r.idusuario
        and r.idencuesta = us.idencuesta
        and n.id = p.nivelorganizacional

        order by r.puntos DESC, us.segundos ASC";

        $consulta = $em->createQuery($dql)->setParameters(array(
                                                                 'encuesta' => $idencuesta,
                                                              ));

        //$consulta = $em->createQuery($dql)->setMaxResults(5);
        $resultados = $consulta->getResult();


        $dql = "select count(u.id) from UsuarioBundle:User u
                where u.isActive=:activo";
        $consulta = $em->createQuery($dql)->setParameters(array( 'activo'=> 'TRUE'));
        $usuariostotales = $consulta->getResult();
        $usuariostotales = $usuariostotales[0][1];

        $dql = "select count(r.idusuario) from EncuestaBundle:Resultados r
                where r.idencuesta=:idencuesta";
        $consulta = $em->createQuery($dql)->setParameters(array( 'idencuesta'=> $idencuesta));
        $usuariosvotantes = $consulta->getResult();
        $usuariosvotantes = $usuariosvotantes[0][1];

        $grafico1 = 0;
        $grafico2 = 0;
        $grafico1 ="['Usuarios Totales',".$usuariostotales."],";
        $grafico2 ="['Usuarios Votantes',".$usuariosvotantes."],";
        //$grafico .="['Usuarios Votantes',".$usuariosvotantes."],";

        //$grafico = substr($grafico, 0, -1);

        return $this->render('EncuestaBundle:Resultados:index.html.twig', array(
            'datosgrafico1'     => $grafico1,
            'datosgrafico2'     => $grafico2,
            'encuesta'          => $encuesta,
            'resultados'        => $resultados
        ));
    }
    /**
     * Creates a new Resultados entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Resultados();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('resultados_show', array('id' => $entity->getId())));
        }

        return $this->render('EncuestaBundle:Resultados:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Resultados entity.
     *
     * @param Resultados $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Resultados $entity)
    {
        $form = $this->createForm(new ResultadosType(), $entity, array(
            'action' => $this->generateUrl('resultados_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Resultados entity.
     *
     */
    public function newAction()
    {
        $entity = new Resultados();
        $form   = $this->createCreateForm($entity);

        return $this->render('EncuestaBundle:Resultados:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Resultados entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Resultados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Resultados entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Resultados:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Resultados entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Resultados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Resultados entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Resultados:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Resultados entity.
    *
    * @param Resultados $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Resultados $entity)
    {
        $form = $this->createForm(new ResultadosType(), $entity, array(
            'action' => $this->generateUrl('resultados_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Resultados entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Resultados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Resultados entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('resultados_edit', array('id' => $id)));
        }

        return $this->render('EncuestaBundle:Resultados:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Resultados entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EncuestaBundle:Resultados')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Resultados entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('resultados'));
    }

    /**
     * Creates a form to delete a Resultados entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('resultados_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
