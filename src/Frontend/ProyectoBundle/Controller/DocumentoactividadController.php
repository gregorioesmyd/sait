<?php

namespace Frontend\ProyectoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProyectoBundle\Entity\Documentoactividad;
use Frontend\ProyectoBundle\Form\DocumentoactividadType;

/**
 * Documento controller.
 *
 */
class DocumentoactividadController extends Controller
{

    /**
     * Lists all Documentoactividad entities.
     *
     */
    public function indexAction($actividad)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Documentoactividad')->findByActividadId($actividad);
        $actividad = $em->getRepository('ProyectoBundle:Actividad')->find($actividad);

        $cont = 0;
        $entities= array();
        $i = 0;
        foreach ($entity as $key) 
        {
            $id[$key->getId()]=$key->getId();
            $entities[$i]['id'] = $id[$key->getId()];

            $proyect[$key->getId()]=$key->getActividadId();
            $entities[$i]['proyecto'] = $proyect[$key->getId()];

            $file[$key->getId()]=$key->getArchivo();
            $entities[$i]['file'] = $file[$key->getId()];

            $descripcion[$key->getId()]=$key->getDescripcion();
            $entities[$i]['descripcion'] = $descripcion[$key->getId()];

            $cont ++;
            $i++;
        }

        return $this->render('ProyectoBundle:Documentoactividad:index.html.twig', array(
            'entities' => $entities,
            'actividad'    => $actividad,
        ));
   }
    /**
     * Creates a new Documentoactividad entity.
     *
     */
    public function createAction(Request $request, $actividad)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Documentoactividad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $actividad = $em->getRepository('ProyectoBundle:Actividad')->find($actividad);

        if ($form->isValid()) {

            if(!empty($form['file']))
            {
                $file=$form['file']->getData();

                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $extension = $file->guessExtension();
                $nombre=$file->getClientOriginalName();
                $nombre=explode(".", $nombre);
                $nombre=$nombre[0];

                #VALIDACIONES DE TAMAÑO Y EXTENSION

                $alert = 0;
                //valido tamaño
                if ($tamaño>2000) {
                    $alert = 1;
                    $this->get('session')->getFlashBag()->add('alert', 'El archivo no puede ser mayor a 2MB.');

                }
                $extensiones=array('jpg','jpeg','png','gif','doc','odt','xls','xlsx','docx','pdf','ppt','pptx');
                //valido las extensiones
                if (!array_search($extension,$extensiones)) {
                    $alert = 1;
                    $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                }
                if($alert == 1)
                {
                    return $this->render('ProyectoBundle:Documentoactividad:new.html.twig', array(
                        'entity' => $entity,
                        'actividad'  => $actividad,
                        'form'   => $form->createView(),
                    ));
                }

                $nombre=str_replace(array(" ","/",".","_","-"),array("","","","",""),trim($nombre));

                //GUARDO EL ARCHIVO
                if($file->move('/var/www/uploads/documentosproyectos/',$nombre.'_'.\date("Gis").'.'.$extension) )
                {
                     $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
                }
                $entity->setActividadId($actividad);

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('documentoactividad_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('ProyectoBundle:Documentoactividad:new.html.twig', array(
                        'entity' => $entity,
                        'actividad'  => $actividad,
                        'form'   => $form->createView(),
                    ));
    }

    /**
     * Creates a form to create a Documentoactividad entity.
     *
     * @param Documentoactividad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Documentoactividad $entity)
    {
        $form = $this->createForm(new DocumentoactividadType(), $entity, array(
            'action' => $this->generateUrl('documentoactividad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Documentoactividad entity.
     *
     */
    public function newAction($actividad)
    {
        $entity = new Documentoactividad();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();
        $actividad = $em->getRepository('ProyectoBundle:Actividad')->find($actividad);


        return $this->render('ProyectoBundle:Documentoactividad:new.html.twig', array(
            'entity' => $entity,
            'actividad'  => $actividad,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Documentoactividad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Documentoactividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documentoactividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $actividad = $entity->getActividadId();
        return $this->render('ProyectoBundle:Documentoactividad:show.html.twig', array(
            'entity'      => $entity,
            'actividad'    => $actividad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Documentoactividad entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Documentoactividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documentoactividad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyectoBundle:Documentoactividad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Documentoactividad entity.
    *
    * @param Documentoactividad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Documentoactividad $entity)
    {
        $form = $this->createForm(new DocumentoactividadType(), $entity, array(
            'action' => $this->generateUrl('documento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Documentoactividad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyectoBundle:Documentoactividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documentoactividad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('documento_edit', array('id' => $id)));
        }

        return $this->render('ProyectoBundle:Documentoactividad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Documentoactividad entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyectoBundle:Documentoactividad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Documentoactividad entity.');
            }
            $actividad = $entity->getActividadId();
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('documentoactividad', array('actividad' => $actividad->getId())));

    }

    /**
     * Creates a form to delete a Documentoactividad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('documentoactividad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
