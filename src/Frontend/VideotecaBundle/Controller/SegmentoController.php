<?php

namespace Frontend\VideotecaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\VideotecaBundle\Entity\WrapperSegmento\SgtDeporte;
use Frontend\VideotecaBundle\Form\Filter\FilterType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Segmento controller.
 *
 */
class SegmentoController extends Controller {


    public function reporteExcel($segmento){

        $html= $this->render("VideotecaBundle:inc:reporteexcel.html.twig", array('segmento'=>$segmento));
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=videoteca.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //damos salida a la tabla
        echo $html->getContent();
        die;
    }
    public function indexAction(Request $request, $page) {

        $em = $this->getDoctrine()->getManager();
        //  Obtener todos los parametros enviados por formulario de filtros
        $inputs = $request->request->all();

        $tipoCinta = null;

        $method = $request->getMethod();
        //  Verificar si se estan filtrandos los datos
        if ($method === 'POST' && isset($inputs)) {
            if (\array_key_exists('cinta', $inputs['form'])) {
                if (!empty($inputs['form']['cinta']['tipoCinta'])) {
                    $tipoCinta = $em->getRepository('VideotecaBundle:TipoCinta')
                            ->findOneById($inputs['form']['cinta']['tipoCinta']);
                }
            }

            $inputs = $inputs['form'];

        }

        //  GENERAR EL FORMULARIO DINAMICAMENTE
        $form = $this->filterForm($tipoCinta);
        $form->handleRequest($request, $form);

        //  OBTENER EL QUERY
        $segmento = $this->get('videoteca.service.segmento');
        $query = $segmento->getQuery($request->getMethod(), $inputs, $tipoCinta);

        //REPORTE EN EXCELL
            $data=$request->request->all();
            if($data):
                $reporte=$data['reporte'];
                if($reporte==1):
                    $segmento = $query->getResult();
                    $this->reporteExcel($segmento);
                endif;
            endif;
      
        //  PAGINAR EL QUERY
        $paginator = $this->get('knp_paginator');
        $pagination = $segmento->pagination($paginator, $page, $query);
        //OBTENER LOS PRETAMOS DEL USUARIO
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $tmp = $em->getRepository('VideotecaBundle:TmpPrestamo')->findByPrestamista($idusuario);
        $prestamo = $this->get('videoteca.service.prestamo');
        $countPrestamo = $prestamo->countPrestamoUser($user);


        if ($method == 'POST' && isset($pagination[0]) && isset($inputs)) {
            // $inputsActive = $segmento->getInputsActive($pagination[0]);
            $inputsActive = $segmento->getInputsActive($inputs, $pagination[0]);
        } else {
            $inputsActive = array();
        }

        return $this->render("VideotecaBundle:WrapperSegmento:Segmento\index.html.twig", array(
                    'pagination'    => $pagination,
                    'tmcinta'       => $countPrestamo,
                    'prestamo'      => $prestamo,
                    'tmp'           => $tmp,
                    'form'          => $form->createView(),
                    'inputs'        => $inputsActive
        ));
    }

    /**
     * Creates a form to create a Categoria entity.
     *
     * @param Categoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function filterForm($tipoCinta) {
        $form = $this->createForm(new FilterType($tipoCinta), array(
            'action' => $this->generateUrl('segmento'),
            'method' => 'POST',
        ));

        $form->add('submit', 'button', array('label' => 'Buscar', 'attr' => array(
                'class' => 'btn btn-default searchBtn btn-submit'
        )));
        $form->add('reset', 'reset', array('label' => 'Limpiar', 'attr' => array(
                'class' => 'btn btn-warning btn-reset'
        )));

        return $form;
    }

    /**
     * Creates a new SgtDeporte entity.
     *
     */
    public function createAction(Request $request, $idCinta, $slug) {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $pathClass = "Frontend\VideotecaBundle\Entity\WrapperSegmento\\$nameClass";
        $entity = new $pathClass();
        $form = $this->createCreateForm($entity, $nameClass, $slug, $idCinta);
        $form->handleRequest($request);
        $cinta = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->findOneById($idCinta);

        if ($form->isValid()) {
            $entity->setCinta($cinta);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('segmento_show', array('id' => $entity->getId(), 'slug' => $slug)));
        }

        return $this->render("VideotecaBundle:WrapperSegmento:" . $nameClass . "\\new.html.twig", array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'cinta' => $cinta,
        ));
    }

    /**
     * Creates a form to create a SgtDeporte entity.
     *
     * @param SgtDeporte $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity, $nameClass, $slug, $idCinta) {
        $type = "Frontend\VideotecaBundle\Form\WrapperSegmento\\" . $nameClass . "Type";
        $form = $this->createForm(new $type(), $entity, array(
            'action' => $this->generateUrl('segmento_create', array(
                'idCinta' => $idCinta,
                'slug' => $slug)
            ),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'GUARDAR',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * [getNameClase description]
     *
     * @param  [type] $slug [description]
     *
     * @return [type]       [description]
     */
    private function getNameClase($slug) {
        $nameClass = "undefined";
        if ($slug == "deporte") {
            $nameClass = "SgtDeporte";
        } elseif ($slug == "satelite") {
            $nameClass = "SgtSatelite";
        } elseif ($slug == "prensa") {
            $nameClass = "SgtPrensa";
        } elseif ($slug == "videoteca") {
            $nameClass = "SgtVideoteca";
        }

        return $nameClass;
    }

    /**
     * Displays a form to create a new SgtDeporte entity.
     *
     */
    public function newAction($idCinta, $slug) {
        $nameClass = $this->getNameClase($slug);
        $pathClass = "Frontend\VideotecaBundle\Entity\WrapperSegmento\\$nameClass";
        $entity = new $pathClass();
        $em = $this->getDoctrine()->getManager();
        $cinta = $em->getRepository('VideotecaBundle:WrapperCinta\Cinta')->find($idCinta);
        $entity->setCinta($cinta);
// Cologar un try-catch para validar que la cinta existe
        $form = $this->createCreateForm($entity, $nameClass, $slug, $cinta->getId());
        return $this->render("VideotecaBundle:WrapperSegmento:" . $nameClass . "\\new.html.twig", array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'cinta' => $cinta,
        ));
    }

    /**
     * Finds and displays a SgtDeporte entity.
     *
     */
    public function showAction($id, $slug) {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $segmento = $em->getRepository('VideotecaBundle:WrapperSegmento\Segmento')->find($id);

        if (!$segmento) {
            throw $this->createNotFoundException("Unable to find $nameClass entity.");
        }

        $deleteForm = $this->createDeleteForm($id, $slug);

        return $this->render("VideotecaBundle:WrapperSegmento:" . $nameClass . "\show.html.twig", array(
                    'segmento' => $segmento,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SgtDeporte entity.
     *
     */
    public function editAction($id, $slug) {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $entity = $em->getRepository('VideotecaBundle:WrapperSegmento\Segmento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find $nameClass entity.");
        }

        $editForm = $this->createEditForm($entity, $nameClass, $slug);
        $deleteForm = $this->createDeleteForm($id, $slug);

        return $this->render("VideotecaBundle:WrapperSegmento:" . $nameClass . "\\edit.html.twig", array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a SgtDeporte entity.
     *
     * @param SgtDeporte $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity, $nameClass, $slug) {
        $type = "Frontend\VideotecaBundle\Form\WrapperSegmento\\" . $nameClass . "Type";
        $form = $this->createForm(new $type(), $entity, array(
            'action' => $this->generateUrl('segmento_update', array('id' => $entity->getId(), 'slug' => $slug)
            ),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Guardar',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * Edits an existing SgtDeporte entity.
     *
     */
    public function updateAction(Request $request, $id, $slug) {
        $em = $this->getDoctrine()->getManager();
        $nameClass = $this->getNameClase($slug);
        $entity = $em->getRepository('VideotecaBundle:WrapperSegmento\Segmento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException("Unable to find $nombreClase entity.");
        }

        $deleteForm = $this->createDeleteForm($id, $slug);
        $editForm = $this->createEditForm($entity, $nameClass, $slug);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('segmento_show', array('id' => $id, 'slug' => $slug)));
        }

        return $this->render("VideotecaBundle:WrapperSegmento:" . $nameClass . "\edit.html.twig", array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SgtDeporte entity.
     *
     */
    public function deleteAction(Request $request, $id, $slug) {
        $nameClass = $this->getNameClase($slug);
        $form = $this->createDeleteForm($id, $slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('VideotecaBundle:WrapperSegmento\Segmento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException("Unable to find $nameClass entity.");
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('segmento'));
    }

    /**
     * Creates a form to delete a SgtDeporte entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $slug) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('segmento_delete', array('id' => $id, 'slug' => $slug)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'label' => 'Eliminar',
                            'attr' => array('class' => 'btn btn-danger')
                        ))
                        ->getForm()
        ;
    }

    public function listAction($parameters) {
        print_r($parameters);
    }

}
