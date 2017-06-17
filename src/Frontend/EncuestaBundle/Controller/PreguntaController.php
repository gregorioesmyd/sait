<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EncuestaBundle\Entity\Pregunta;
use Frontend\EncuestaBundle\Form\PreguntaType;

use Frontend\EncuestaBundle\Entity\Encuesta;
use Frontend\EncuestaBundle\Form\EncuestaType;

use Frontend\EncuestaBundle\Entity\Respuesta;
use Frontend\EncuestaBundle\Form\RespuestaType;

/**
 * Pregunta controller.
 *
 */
class PreguntaController extends Controller
{

    /**
     * Lists all Pregunta entities.
     *
     */
    public function indexAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $entities = $em->getRepository('EncuestaBundle:Pregunta')->findByIdencuesta($idencuesta);

        return $this->render('EncuestaBundle:Pregunta:index.html.twig', array(
            'entities'    => $entities,
            'encuesta'    => $encuesta,
        ));
    }
    /**
     * Creates a new Pregunta entity.
     *
     */
    public function createAction(Request $request, $idencuesta)
    {
      $em = $this->getDoctrine()->getManager();

      $ancho_debe =$this->container->getParameter('ancho');
      $alto_debe = $this->container->getParameter('alto');

      $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
      $encuesta = $encuesta[0];

        $entity = new Pregunta();
        $form = $this->createCreateForm($entity,$encuesta);
        $form->handleRequest($request);

        if ($form->isValid()) {
          //procesar archivo
          if($form['file']->getData()){

              $file=$form['file']->getData();

              $size = GetImageSize("$file");
              $ancho_foto = $size[0];
              $alto_foto = $size[1];

              $alert = false;
              if($ancho_foto > $ancho_debe and $alto_foto > $alto_debe)
              {
                $alert = true;

                $mensaje = "La imagen que intenta subir es muy grande, la misma debe tener las siguientes medidas: ".$ancho_debe."ancho x".$alto_debe." alto";
              }elseif($ancho_foto < $ancho_debe and $alto_foto < $alto_debe) {
                $alert = true;
                $mensaje = "La imagen que intenta subir es muy pequeña, la misma debe tener las siguientes medidas: ".$ancho_debe."ancho x".$alto_debe." alto";
              }

              if($alert == true)
              {
                $this->get('session')->getFlashBag()->add('alert', $mensaje);
                return $this->render('EncuestaBundle:Pregunta:new.html.twig', array(
                    'entity'    => $entity,
                    'encuesta'  => $encuesta,
                    'ancho'     => $ancho_debe,
                    'alto'      => $alto_debe,
                    'form'      => $form->createView(),
                ));
              }

              $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
              $nombre=$file->getClientOriginalName();
              $nombreLargo=explode(".", $nombre);
              $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
              $extension = $nombreLargo[1];

              //validaciones
                  $extensiones=array('jpg','jpeg','png');
                  if (!in_array($extension,$extensiones)) {
                      $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido, deber en formato jpg, jpeg o png.');
                      return $this->render('EncuestaBundle:Pregunta:edit.html.twig', array(
                          'entity'      => $entity,
                          'idencuesta'  => $encuesta,
                          'ancho'       => $ancho_debe,
                          'alto'        => $alto_debe,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                      ));
                  }
              //fin

              if($file->move('/var/www/uploads/encuestas/',$nombre.'_'.\date("Gis").'.'.$extension) )
              {
                  $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
              }

          //fin procesar archivo
        }
            $entity->setidencuesta($encuesta);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pregunta_show', array('id' => $entity->getId(), 'idencuesta'=> $encuesta->getId())));
        }

        return $this->render('EncuestaBundle:Pregunta:new.html.twig', array(
            'entity' => $entity,
            'encuesta'  => $encuesta,
            'ancho'     => $ancho_debe,
            'alto'      => $alto_debe,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Pregunta entity.
     *
     * @param Pregunta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pregunta $entity, $encuesta)
    {
        $form = $this->createForm(new PreguntaType(), $entity, array(
            'action' => $this->generateUrl('pregunta_create', array('idencuesta' => $encuesta->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pregunta entity.
     *
     */
    public function newAction($idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $ancho_debe =$this->container->getParameter('ancho');
        $alto_debe = $this->container->getParameter('alto');

        $entity = new Pregunta();
        $form   = $this->createCreateForm($entity, $encuesta);

        return $this->render('EncuestaBundle:Pregunta:new.html.twig', array(
            'entity'    => $entity,
            'encuesta'  => $encuesta,
            'ancho'     => $ancho_debe,
            'alto'      => $alto_debe,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Pregunta entity.
     *
     */
    public function showAction($id,$idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $entity = $em->getRepository('EncuestaBundle:Pregunta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pregunta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Pregunta:show.html.twig', array(
            'entity'      => $entity,
            'encuesta'    => $encuesta,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Pregunta entity.
     *
     */
    public function editAction($id, $idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $entity = $em->getRepository('EncuestaBundle:Pregunta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pregunta entity.');
        }

        $ancho_debe =$this->container->getParameter('ancho');
        $alto_debe = $this->container->getParameter('alto');

        $editForm = $this->createEditForm($entity, $encuesta);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Pregunta:edit.html.twig', array(
            'entity'      => $entity,
            'encuesta'    => $encuesta,
            'ancho'       => $ancho_debe,
            'alto'        => $alto_debe,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Pregunta entity.
    *
    * @param Pregunta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pregunta $entity, $encuesta)
    {
        $form = $this->createForm(new PreguntaType(), $entity, array(
            'action' => $this->generateUrl('pregunta_update', array('id' => $entity->getId(),'idencuesta' => $encuesta->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pregunta entity.
     *
     */
    public function updateAction(Request $request, $id, $idencuesta)
    {
        $em = $this->getDoctrine()->getManager();

        $ancho_debe =$this->container->getParameter('ancho');
        $alto_debe = $this->container->getParameter('alto');

        $entity = $em->getRepository('EncuestaBundle:Pregunta')->find($id);

        $encuesta = $em->getRepository('EncuestaBundle:Encuesta')->findById($idencuesta);
        $encuesta = $encuesta[0];

        $archivo = $entity->getArchivo();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pregunta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $encuesta);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
          //procesar archivo
          if($editForm['file']->getData()){

              $file=$editForm['file']->getData();

              $size = GetImageSize("$file");
              $ancho_foto = $size[0];
              $alto_foto = $size[1];

              $alert = false;

              if($ancho_foto > $ancho_debe and $alto_foto > $alto_debe)
              {
                $alert = true;

                $mensaje = "La imagen que intenta subir es muy grande, la misma debe tener las siguientes medidas: ".$ancho_debe."ancho x".$alto_debe." alto";
              }elseif($ancho_foto < $ancho_debe and $alto_foto < $alto_debe) {
                $alert = true;
                $mensaje = "La imagen que intenta subir es muy pequeña, la misma debe tener las siguientes medidas: ".$ancho_debe."ancho x".$alto_debe." alto";
              }


              if($alert == true)
              {
                $this->get('session')->getFlashBag()->add('alert', $mensaje);
                return $this->render('EncuestaBundle:Pregunta:new.html.twig', array(
                    'entity'    => $entity,
                    'encuesta'  => $encuesta,
                    'ancho'     => $ancho_debe,
                    'alto'      => $alto_debe,
                    'form'      => $form->createView(),
                ));
              }
              $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
              $nombre=$file->getClientOriginalName();
              $nombreLargo=explode(".", $nombre);
              $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
              $extension = $nombreLargo[1];

              //validaciones
                  $extensiones=array('jpg','jpeg','png');
                  if (!in_array($extension,$extensiones)) {
                      $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                      return $this->render('EncuestaBundle:Pregunta:edit.html.twig', array(
                          'entity'      => $entity,
                          'idencuesta'    => $encuesta,
                          'ancho'       => $ancho_debe,
                          'alto'        => $alto_debe,
                          'edit_form'   => $editForm->createView(),
                          'delete_form' => $deleteForm->createView(),
                      ));
                  }
              //fin

              if($file->move('/var/www/uploads/encuestas/',$nombre.'_'.\date("Gis").'.'.$extension) )
              {
                  $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
              }

          //fin procesar archivo
        }else {
            $entity->setArchivo($archivo);
          }
            $entity->setidencuesta($encuesta);
            $em->flush();

            return $this->redirect($this->generateUrl('pregunta_show', array('id' => $id, 'idencuesta' => $encuesta->getId())));
        }

        return $this->render('EncuestaBundle:Pregunta:edit.html.twig', array(
            'entity'      => $entity,
            'idencuesta'    => $encuesta,
            'ancho'       => $ancho_debe,
            'alto'        => $alto_debe,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Pregunta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EncuestaBundle:Pregunta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pregunta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pregunta_index'));
    }

    /**
     * Creates a form to delete a Pregunta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pregunta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
