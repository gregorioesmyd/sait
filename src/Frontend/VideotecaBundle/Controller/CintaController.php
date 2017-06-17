<?php

namespace Frontend\VideotecaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta;
use Frontend\VideotecaBundle\Form\WrapperCinta\CintaType;
use Frontend\VideotecaBundle\Form\WrapperCinta\CntDeporteType;
use Frontend\VideotecaBundle\Form\WrapperCinta\CntVideotecaType;
use Frontend\VideotecaBundle\Form\WrapperCinta\CntSateliteType;
use Frontend\VideotecaBundle\Form\WrapperCinta\CntPrensaType;

/**
 * Cinta controller.
 *
 */
class CintaController extends Controller
{
    /**
     * List Cintas
     *
     */
    public function indexAction()
    {
        return $this->render('VideotecaBundle:WrapperCinta:Cinta\index.html.twig');
    }

    /**
     * Creates a new Cinta entity.
     *
     */
    public function createAction(Request $request, $slug)
    {
        $nameClass = $this->getNameClase($slug);
        $entity = new Cinta();
        $form = $this->createCreateForm($entity, $nameClass, $slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
            $user = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);
            $entity->setDocumentalista($user);
            $cinta = $this->get('videoteca.service.cinta');
            $cinta->save($entity, $slug, $user);
            return $this->redirect($this->generateUrl('cinta_show', array('id' => $entity->getId(), 'slug' =>  $slug)));
        }

        $path = "VideotecaBundle:WrapperCinta:".$nameClass."\\new.html.twig";

        return $this->render("$path", array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'visible' => true,
        ));
    }

    /**
     * Creates a form to create a CntDeporte entity.
     *
     * @param CntDeporte $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity, $nameClass, $slug)
    {
        $type = "Frontend\VideotecaBundle\Form\WrapperCinta\\" . $nameClass . "Type";
        $form = $this->createForm(new $type($this->obtenerTipoCinta($slug)), $entity, array(
            'action' => $this->generateUrl('cinta_create', array('slug' => $slug)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',
                array('label' => 'GUARDAR',
                      'attr' => array('class' => 'btn btn-default')
            ));

        return $form;
    }

    private function obtenerTipoCinta($slug)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('VideotecaBundle:TipoCinta')
                    ->findOneBySlug($slug);
    }

    /**
     * [getNameClase description]
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    private function getNameClase($slug)
    {
        $nameClass = "undefined";
        if ($slug == "deporte") {
            $nameClass = "CntDeporte";
        }elseif ($slug == "satelite") {
            $nameClass = "CntSatelite";
        }elseif ($slug == "prensa") {
            $nameClass = "CntPrensa";
        }elseif ($slug == "videoteca") {
            $nameClass = "CntVideoteca";
        }else{
            $nameClass = "de";
        }

        return $nameClass;
    }

    /**
     * Displays a form to create a new CntDeporte entity.
     *
     */
    public function newAction($slug)
    {
        if($slug != "default"){
            $nameClass = $this->getNameClase($slug);
            $entity = new Cinta();
            $form   = $this->createCreateForm($entity, $nameClass, $slug);
            $path = "VideotecaBundle:WrapperCinta:".$nameClass."\\new.html.twig";

            return $this->render("$path", array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'visible' => true,
            ));
            
        }else {
            $path = "VideotecaBundle:WrapperCinta:Cinta/new.html.twig";

            return $this->render("$path", array('visible' => false));
        }
    }

    /**
     * Finds and displays a CntDeporte entity.
     *
     */
    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $cinta = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($id);

        if (!$cinta) {
            throw $this->createNotFoundException("Unable to find $nameClass entity.");
        }

        $segmentos = $em->getRepository('VideotecaBundle:WrapperSegmento\Segmento')->findByCinta($id);

        return $this->render("VideotecaBundle:WrapperCinta:".$nameClass."\\show.html.twig", array(
            'cinta'      => $cinta,
            'segmentos'   => $segmentos,
        ));
    }

    /**
     * Displays a form to edit an existing CntDeporte entity.
     *
     */
    public function editAction($id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $entity = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find $nameClass entity.");
        }

        $editForm = $this->createEditForm($entity, $nameClass, $slug);
        $deleteForm = $this->createDeleteForm($id, $slug);
        $path = "VideotecaBundle:WrapperCinta:".$nameClass."\\edit.html.twig";

        return $this->render("$path", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CntDeporte entity.
    *
    * @param CntDeporte $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm($entity, $nameClass, $slug)
    {
        $type = "Frontend\VideotecaBundle\Form\WrapperCinta\\" . $nameClass . "Type";
        $form = $this->createForm(new $type($entity->getTipoCinta()), $entity, array(
            'action' => $this->generateUrl('cinta_update',
                    array('id' => $entity->getId(), 'slug' => $slug)
                ),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
                'label' => 'Guardar',
                'attr'  => array('class' => 'btn btn-default' )
            ));

        return $form;
    }
    /**
     * Edits an existing CntDeporte entity.
     *
     */
    public function updateAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $entity = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find $nameClass entity.");
        }

        $deleteForm = $this->createDeleteForm($id, $slug);
        $editForm = $this->createEditForm($entity, $nameClass, $slug);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cinta_show', array('id' => $entity->getId(), 'slug' =>  $slug)));
        }

        return $this->render("VideotecaBundle:WrapperCinta:".$nameClass."\\edit.html.twig", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CntVideoteca entity.
     *
     */
    public function deleteAction(Request $request, $id, $slug)
    {
        $nameClass = $this->getNameClase($slug);
        $form = $this->createDeleteForm($id, $slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException("Unable to find $nameClass entity.");
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cinta'));
    }

    /**
     * Creates a form to delete a CntVideoteca entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $slug)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cinta_delete', array('id' => $id, 'slug' =>  $slug)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                    'label' => 'Eliminar',
                    'attr'  => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }

}
