<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\PrincipalBundle\Entity\Jornadalaboral;
use Progis\PrincipalBundle\Form\JornadalaboralType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Jornadalaboral controller.
 *
 */
class JornadalaboralController extends Controller
{
    
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }

    /**
     * Lists all Jornadalaboral entities.
     *
     */
    public function indexAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PrincipalBundle:Jornadalaboral')->findAll();

        return $this->render('PrincipalBundle:Jornadalaboral:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    function diasFormatoParaGuardar($datos){
        
        $dias=null;
        foreach ($datos as $d) {
            $dias .= $d.'-';
        }
        $dias=  substr($dias, 0,-1);
        return $dias;
    }
    
    public function createAction(Request $request)
    {
        $this->seguridad();
        
        $entity = new Jornadalaboral();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $datos=$request->get('progis_principalbundle_jornadalaboral');
            $dias=$this->diasFormatoParaGuardar($datos['dias']);
            
            $entity->setDias($dias);
            
            $dataForm=$form->getData();
            $existeDias = $em->getRepository('PrincipalBundle:Jornadalaboral')->findBy(array('dias'=>$dias,'horadesde'=>$dataForm->getHoradesde(),'horahasta'=>$dataForm->getHorahasta()));
            if(!empty($existeDias)):
                $this->get('session')->getFlashBag()->add('alert', 'La jornada que intenta guardar ya existe.');
                return $this->render('PrincipalBundle:Jornadalaboral:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            endif;
            
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Jornada creada con exito.');
            return $this->redirect($this->generateUrl('jornadalaboral'));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('PrincipalBundle:Jornadalaboral:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Jornadalaboral entity.
     *
     * @param Jornadalaboral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Jornadalaboral $entity)
    {
        $form = $this->createForm(new JornadalaboralType(), $entity, array(
            'action' => $this->generateUrl('jornadalaboral_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Jornadalaboral entity.
     *
     */
    public function newAction()
    {
        $this->seguridad();
        
        $entity = new Jornadalaboral();
        $form   = $this->createCreateForm($entity);

        return $this->render('PrincipalBundle:Jornadalaboral:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Jornadalaboral entity.
     *
     */
    public function showAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Jornadalaboral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jornadalaboral entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PrincipalBundle:Jornadalaboral:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Jornadalaboral entity.
     *
     */
    public function editAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Jornadalaboral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jornadalaboral entity.');
        }

        $dias= explode("-", $entity->getDias());
        $entity->setDias($dias);
        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PrincipalBundle:Jornadalaboral:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Jornadalaboral entity.
    *
    * @param Jornadalaboral $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Jornadalaboral $entity)
    {
        $form = $this->createForm(new JornadalaboralType(), $entity, array(
            'action' => $this->generateUrl('jornadalaboral_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Jornadalaboral entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PrincipalBundle:Jornadalaboral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jornadalaboral entity.');
        }

        
        $dias= explode("-", $entity->getDias());
        $entity->setDias($dias);
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $datos=$editForm->getData();
            $dias=$this->diasFormatoParaGuardar($datos->getDias());
            $datos->setDias($dias);
            
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Jornada editada exitosamente.');
            return $this->redirect($this->generateUrl('jornadalaboral_edit', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('PrincipalBundle:Jornadalaboral:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Jornadalaboral entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PrincipalBundle:Jornadalaboral')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Jornadalaboral entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add('notice', 'Jornada eliminada con exito.');
        return $this->redirect($this->generateUrl('jornadalaboral'));
    }

    /**
     * Creates a form to delete a Jornadalaboral entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('jornadalaboral_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
