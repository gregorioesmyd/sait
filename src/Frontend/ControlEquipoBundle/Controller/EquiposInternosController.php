<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Frontend\ControlEquipoBundle\Entity\EquiposInternos;
use Frontend\ControlEquipoBundle\Form\EquiposInternosType;

/**
 * EquiposInternos controller.
 *
 */
class EquiposInternosController extends Controller
{
    /**
     * Lists all EquiposInternos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ControlEquipoBundle:EquiposInternos')->findAll();
        
        if(!empty($entities)) {
            foreach ($entities as $entity) {
                $unidad[$entity->getId()] = $entity->getResponsablePatrimonial()->getNivelorganizacional(); 
            }
        } else {
            $unidad = null;
        }
        
        return $this->render('ControlEquipoBundle:EquiposInternos:index.html.twig', array(
            'entities'  => $entities,
            'unidadResponsable'    => $unidad,
        ));
    }

    /**
     * Creates a new EquiposInternos entity.
     *
     */
    public function createAction(Request $request)
    {
        
        $data = $request->request->all();
        $marca = $data['controlequipobundle_equipointerno']['marca'];
       
        if($marca == null) {
            $marca = 0;
        }
        $entity = new EquiposInternos();
        $form = $this->createCreateForm($entity,$marca);
        $form->handleRequest($request);
        //$em     = $this->getDoctrine()->getManager();
        //$marcas = $em->getRepository('ControlEquipoBundle:Marcas')->findAll();
        
        
        if ($form->isValid()) {
            // Para guardar la foto
            if($form['file']->getData())
            {
                $file=$form['file']->getData();
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $extension = $file->guessExtension();
                //$nombre=$file->getClientOriginalName();
                //$nombre=explode(".", $nombre);
                
                $nombre=$entity->getSerial();

                //valido tamaño
                if ($tamaño>2000) {
                    $this->get('session')->getFlashBag()->add('alert', 'El archivo no puede ser mayor a 2MB.');

                    return $this->render('ControlEquipo:Contratos:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                        //'marcas' => $marcas
                    ));

                }
                $extensiones=array('jpg','jpeg','png','gif');
                //valido las extensiones
                if (!array_search($extension,$extensiones)) {
                    $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');

                    return $this->render('ControlEquipoBundle:EquiposInternos:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                        //'marcas' => $marcas
                    ));
                }
                
                $nombre=str_replace(array(" ","/",".","_","-"),array("","","","",""),trim($nombre));

                //GUARDO EL ARCHIVO
                if($file->move('uploads/controlequipo/',$nombre.'_'.\date("Y-m-d").'.'.$extension) )
                {
                     $entity->setFotoReferencia($nombre.'_'.\date("Y-m-d").'.'.$extension);
                }
            }
            $em = $this->getDoctrine()->getManager();
            // Guardamos la fecha actual
            $creado = date_create_from_format('Y-m-d', \date("Y-m-d"));
            $entity->setCreado($creado);
            $entity->setResponsableRegistro($em->getRepository('UsuarioBundle:Perfil')->find(395));
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Equipo Interno registrado exitosamente.');
            return $this->redirect($this->generateUrl('equipointerno_new'));
        }

        
         
        //$form = $this->createForm(new EquiposInternosType($marca),$entity);
        //$form->bind($request);
        
        return $this->render('ControlEquipoBundle:EquiposInternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
            //'marcas' => $marcas
        ));
    }

    /**
     * Creates a form to create a EquiposInternos entity.
     *
     * @param EquiposInternos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EquiposInternos $entity,$marca)
    {
        
        $form = $this->createForm(new EquiposInternosType($marca), $entity, array(
            'action' => $this->generateUrl('equipointerno_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear nuevo Equipo'));

        return $form;
    }

    /**
     * Displays a form to create a new EquiposInternos entity.
     *
     */
    public function newAction()
    {
        /*$entity = new EquiposInternos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:EquiposInternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));*/
        //$em = $this->getDoctrine()->getManager();
        //$tarea = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find();
        
        //genero formulario con los integrantes de la unidad
        //$idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        //$f=new Funcion; 
        //$usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        $em = $this->getDoctrine()->getManager();
        //$marcas = $em->getRepository('ControlEquipoBundle:Marcas')->findAll();
        
        $entity = new EquiposInternos();
        $form   = $this->createCreateForm($entity,0);
        return $this->render('ControlEquipoBundle:EquiposInternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            //'marcas' => $marcas,
            //'idusuario'=>$idusuario
        ));
    }

    /**
     * Finds and displays a EquiposInternos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposInternos entity.');
        }

        $responsable    = $entity->getResponsablePatrimonial()->getNivelorganizacional();
        $marca          = $entity->getModelo()->getMarca();

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:EquiposInternos:show.html.twig', array(
            'entity'            => $entity,
            'delete_form'       => $deleteForm->createView(),
            'marca'             => $marca,
            'unidadResponsable' => $responsable
        ));
    }

    /**
     * Displays a form to edit an existing EquiposInternos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposInternos entity.');
        }
        //$entity->setMarca($entity->getModelo()->getId());
        $marca = $entity->getModelo()->getMarca()->getId();
        //print_r($marca);
        //die;
        $entity->setMarca($marca);
        
        /*if($entity->getFotoReferencia() != '') {
            $path = $this->get('kernel')->getRootDir(). "/web/uploads/controlequipo/" . $entity->getFotoReferencia();
            $content = file_get_contents($path);
            $file = new File($content);
            $entity->setFile($file);
        }*/
        
        $editForm = $this->createEditForm($entity,$marca);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:EquiposInternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EquiposInternos entity.
    *
    * @param EquiposInternos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EquiposInternos $entity,$marca)
    {
        $form = $this->createForm(new EquiposInternosType($marca), $entity, array(
            'action' => $this->generateUrl('equipointerno_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Editar'));

        return $form;
    }
    /**
     * Edits an existing EquiposInternos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EquiposInternos entity.');
        }
        $foto = $entity->getFotoReferencia();
        $data = $request->request->all();
        $marca = $data['controlequipobundle_equipointerno']['marca'];
       
        if($marca == null) {
            $marca = 0;
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity,$marca);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
            $entity->setFotoReferencia($foto);
            /*if($form['file']->getData())
            {
                $file=$form['file']->getData();
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $extension = $file->guessExtension();
                
                $nombre=$entity->getSerial();

                //valido tamaño
                if ($tamaño>2000) {
                    $this->get('session')->getFlashBag()->add('alert', 'El archivo no puede ser mayor a 2MB.');

                    return $this->render('ControlEquipo:Contratos:edit.html.twig', array(
                        'entity' => $entity,
                        'form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        //'marcas' => $marcas
                    ));

                }
                $extensiones=array('jpg','jpeg','png','gif');
                //valido las extensiones
                if (!array_search($extension,$extensiones)) {
                    $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');

                    return $this->render('ControlEquipoBundle:EquiposInternos:edit.html.twig', array(
                        'entity' => $entity,
                        'form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        //'marcas' => $marcas
                    ));
                }
                
                $nombre=str_replace(array(" ","/",".","_","-"),array("","","","",""),trim($nombre));

                //GUARDO EL ARCHIVO
                if($file->move('uploads/controlequipo/',$nombre.'_'.\date("Y-m-d").'.'.$extension) )
                {
                     $entity->setFotoReferencia($nombre.'_'.\date("Y-m-d").'.'.$extension);
                }
            }*/
            //$fotoReferencia = $entity->getFile();
            //$entity->setFotoReferencia($fotoReferencia); 
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Se ha modificado el equipo satisfactoriamente.');
            return $this->redirect($this->generateUrl('equipointerno_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:EquiposInternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EquiposInternos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:EquiposInternos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EquiposInternos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('equipointerno'));
    }

    /**
     * Creates a form to delete a EquiposInternos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipointerno_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
}
