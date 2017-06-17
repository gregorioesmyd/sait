<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Frontend\EncuestaBundle\Entity\Encuesta;
use Frontend\EncuestaBundle\Form\EncuestaType;

use Frontend\EncuestaBundle\Entity\Pregunta;
use Frontend\EncuestaBundle\Form\PreguntaType;

use Frontend\EncuestaBundle\Entity\Resultados;
use Frontend\EncuestaBundle\Form\ResultadosType;


/**
 * Encuesta controller.
 *
 */
class EncuestaController extends Controller
{
      /**
       * Lists all Encuesta entities.
       *
       */
      public function activaAction()
      {
        $em = $this->getDoctrine()->getManager();

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO

        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
    		if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
    		{
    			throw new AccessDeniedException();
    		}

        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $usuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $trivias_activas = $this->get('trivias');

        $activaa = $trivias_activas->activa($usuario);

        $encuestas  = $activaa[0];
        $activa     = $activaa[1];
        $contador   = $activaa[2];
        $puntos     = $activaa[3];
        $voto       = $activaa[4];

        return $this->render('EncuestaBundle:Encuesta:activa.html.twig', array(
            'entities'  => $encuestas,
            'activa'    => $activa,
            'contador'  => $contador,
            'puntos'    => $puntos,
            'voto'      => $voto
        ));
      }

    /**
     * Lists all Encuesta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comezotermino = $this->get('trivias');

        $comenzooo = $comezotermino->comenzotermino();

        $entities   = $comenzooo[0];
        $comenzo    = $comenzooo[1];
        $termino    = $comenzooo[2];

        return $this->render('EncuestaBundle:Encuesta:index.html.twig', array(
            'entities'  => $entities,
            'comenzo'   => $comenzo,
            'termino'   => $termino
        ));
    }
    /**
     * Creates a new Encuesta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Encuesta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $numeropregunta = $entity->getNumeropregunta();

        $puntospreguntas = 100/$numeropregunta;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPuntospregunta($puntospreguntas);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('encuesta_show', array('id' => $entity->getId())));
        }

        return $this->render('EncuestaBundle:Encuesta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Encuesta entity.
     *
     * @param Encuesta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Encuesta $entity)
    {
        $form = $this->createForm(new EncuestaType(), $entity, array(
            'action' => $this->generateUrl('encuesta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Encuesta entity.
     *
     */
    public function newAction()
    {
        $entity = new Encuesta();
        $form   = $this->createCreateForm($entity);

        return $this->render('EncuestaBundle:Encuesta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Encuesta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Encuesta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Encuesta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Encuesta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Encuesta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Encuesta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Encuesta entity.');
        }




        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Encuesta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Encuesta entity.
    *
    * @param Encuesta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Encuesta $entity)
    {
        $form = $this->createForm(new EncuestaType(), $entity, array(
            'action' => $this->generateUrl('encuesta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Encuesta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Encuesta')->find($id);

        $trivias = $this->get('trivias');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Encuesta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $numeropregunta = $entity->getNumeropregunta();

        $puntospreguntas = 100/$numeropregunta;

      /*  $comenzo    = $trivias->comenzotermino()[1];
        $termino    = $trivias->comenzotermino()[2][$id];*/

        $comenzooo = $trivias->comenzotermino();

        $entities   = $comenzooo[0];
        $comenzo    = $comenzooo[1];
        $termino    = $comenzooo[2];


        if($comenzo[$id] == 1 or $termino[$id] == 1)
        {
          $alert = "la encuesta no se puede modificar puesto que ya comenzó";
          $this->get('session')->getFlashBag()->add('alert', $alert);

          return $this->render('EncuestaBundle:Encuesta:index.html.twig', array(
              'entities'  => $entities,
              'comenzo'   => $comenzo,
              'termino'   => $termino
          ));
        }else {
          if ($editForm->isValid()) {
            $entity->setPuntospregunta($puntospreguntas);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('encuesta_show', array('id' => $id)));
          }
        }
        return $this->render('EncuestaBundle:Encuesta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Encuesta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EncuestaBundle:Encuesta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Encuesta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('encuesta'));
    }

    /**
     * Creates a form to delete a Encuesta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('encuesta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
