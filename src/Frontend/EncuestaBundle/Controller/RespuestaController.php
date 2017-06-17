<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EncuestaBundle\Entity\Respuesta;
use Frontend\EncuestaBundle\Form\RespuestaType;

use Frontend\EncuestaBundle\Entity\Pregunta;
use Frontend\EncuestaBundle\Form\PreguntaType;

/**
 * Respuesta controller.
 *
 */
class RespuestaController extends Controller
{
    /**
     * Lists all Respuesta entities.
     *
     */
    public function indexAction($idpregunta)
    {
        $em = $this->getDoctrine()->getManager();

        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta = $pregunta[0];

        $entities = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($idpregunta);

        return $this->render('EncuestaBundle:Respuesta:index.html.twig', array(
            'entities'  => $entities,
            'pregunta'  => $pregunta,
        ));
    }
    /**
     * Creates a new Respuesta entity.
     *
     */
    public function createAction(Request $request, $idpregunta)
    {
        $em = $this->getDoctrine()->getManager();
        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta=$pregunta[0];

        $entity = new Respuesta();
        $form = $this->createCreateForm($entity, $idpregunta);
        $form->handleRequest($request);

        if ($form->isValid()) {

          if($entity->getCorrecta())
          {
            $respuestas = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($idpregunta);

            $contador_correctas = 0;
            foreach ($respuestas as $key) {
              if($key->getCorrecta())
              {
                $contador_correctas ++;
              }
            }
            if ($contador_correctas > 0)
            {
              $this->get('session')->getFlashBag()->add('alert', "Ya existe una respuesta Correcta para esta pregunta");
              return $this->render('EncuestaBundle:Respuesta:new.html.twig', array(
                  'entity'      => $entity,
                  'pregunta'    => $pregunta,
                  'idpregunta'  => $idpregunta,
                  'form'        => $form->createView(),
              ));
            }else {
              $entity->setIdpregunta($pregunta);
              $em->persist($entity);
              $em->flush();
              return $this->redirect($this->generateUrl('respuesta_show', array('id' => $entity->getId(), 'idpregunta' => $idpregunta)));
            }


          }else {
            $entity->setIdpregunta($pregunta);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('respuesta_show', array('id' => $entity->getId(), 'idpregunta' => $idpregunta)));
          }

        }
        return $this->render('EncuestaBundle:Respuesta:new.html.twig', array(
            'entity' => $entity,
            'pregunta'    => $pregunta,
            'idpregunta'  => $idpregunta,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Respuesta entity.
     *
     * @param Respuesta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Respuesta $entity, $idpregunta)
    {
        $form = $this->createForm(new RespuestaType(), $entity, array(
            'action' => $this->generateUrl('respuesta_create', array('idpregunta' => $idpregunta)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Respuesta entity.
     *
     */
    public function newAction($idpregunta)
    {

        $em = $this->getDoctrine()->getManager();
        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta=$pregunta[0];

        $entity = new Respuesta();
        $form   = $this->createCreateForm($entity, $idpregunta);



        return $this->render('EncuestaBundle:Respuesta:new.html.twig', array(
            'entity'      => $entity,
            'pregunta'    => $pregunta,
            'idpregunta'  => $idpregunta,
            'form'        => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Respuesta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Respuesta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Respuesta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Respuesta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Respuesta entity.
     *
     */
    public function editAction($id, $idpregunta)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Respuesta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Respuesta entity.');
        }

        $editForm = $this->createEditForm($entity,$idpregunta);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Respuesta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Respuesta entity.
    *
    * @param Respuesta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Respuesta $entity,$idpregunta)
    {
        $form = $this->createForm(new RespuestaType(), $entity, array(
            'action' => $this->generateUrl('respuesta_update', array('id' => $entity->getId(), 'idpregunta' => $idpregunta)),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Respuesta entity.
     *
     */
    public function updateAction(Request $request, $id, $idpregunta)
    {
        $em = $this->getDoctrine()->getManager();

        $respuestas_pregunta = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($idpregunta);

        $contador_correctas = 0;
        foreach ($respuestas_pregunta as $key) {
          if($key->getCorrecta())
          {
            $contador_correctas ++;
          }
        }

        $entity = $em->getRepository('EncuestaBundle:Respuesta')->find($id);

        $pregunta = $em->getRepository('EncuestaBundle:Pregunta')->findById($idpregunta);
        $pregunta = $pregunta[0];

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Respuesta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $idpregunta);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

          if($entity->getCorrecta())
          {
            if ($contador_correctas > 0)
            {
              $this->get('session')->getFlashBag()->add('alert', "Ya existe una respuesta Correcta para esta pregunta");
              $em = $this->getDoctrine()->getManager();

              $entities = $em->getRepository('EncuestaBundle:Respuesta')->findByIdpregunta($idpregunta);

              return $this->redirect($this->generateUrl('respuesta_index', array('idpregunta' => $idpregunta)));
            }else {
              $entity->setIdpregunta($pregunta);
              $em->persist($entity);
              $em->flush();
              return $this->redirect($this->generateUrl('respuesta_show', array('id' => $entity->getId(), 'idpregunta' => $idpregunta)));
            }

          }else {

            $entity->setIdpregunta($pregunta);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('respuesta_show', array('id' => $entity->getId(), 'idpregunta' => $idpregunta)));
          }

        }

        return $this->render('EncuestaBundle:Respuesta:edit.html.twig', array(
            'entity'      => $entity,
            'idpregunta'  => $idpregunta,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Respuesta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EncuestaBundle:Respuesta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Respuesta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('respuesta'));
    }

    /**
     * Creates a form to delete a Respuesta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('respuesta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
