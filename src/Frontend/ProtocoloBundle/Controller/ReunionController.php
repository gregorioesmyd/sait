<?php

namespace Frontend\ProtocoloBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProtocoloBundle\Entity\Reunion;
use Frontend\ProtocoloBundle\Form\ReunionType;

/**
 * Reunion controller.
 *
 */
class ReunionController extends Controller
{

    /**
     * Lists all Reunion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProtocoloBundle:Reunion')->findAll();

        return $this->render('ProtocoloBundle:Reunion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Reunion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Reunion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $refrigerio = '';
            $refrigerios = array_filter($entity->getRefrigerio(), 'strlen');
            $c = count($refrigerios);
            foreach ($refrigerios as $refri) {
                if ($c > 1) {
                    $refrigerio .= $refri . ',';
                } else {
                    $refrigerio .= $refri;
                }
                --$c;
            }
            
        if ($form->isValid()) {
            $entity->setRefrigerio($refrigerio);
            $em = $this->getDoctrine()->getManager();

            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
            $query = $em->createQuery($dql);
            $entities = $query->getResult();
            $entity->setSolicitante($entities[0]);

            //envio correo

                $datouser=$em->getRepository('UsuarioBundle:User')->find($idusuario);
                $correouser=$datouser->getUsername()."@telesurtv.net";
                $correoto[]=$correouser;
                /*Correo responsables de protocolo*/
                    $parametros['protocolo']=$this->container->getParameter('protocolo');
                    $salapro=explode(',',$parametros['protocolo']);
                    $correoproto = array_push($salapro,$correouser);
                    $correoproto = $salapro;
                /*Fin*/
               $correoaplica="aplicaciones@telesurtv.net";
               $message = \Swift_Message::newInstance()   
                ->setSubject('SOLICITUD DE PROTOCOLO')  
                ->setFrom($correoaplica)     // we configure the sender
                ->setTo($correoproto)   
                ->setBody( $this->renderView(
                        'ProtocoloBundle:Correo:reunion.html.twig',
                        array('reunion' => $entity,
                              'perfil'        => $entities)
                    ), 'text/html');

                $this->get('mailer')->send($message);
            //fin enviar correo
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA ENVIADO EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('protocolo_homepage', array('id' => $entity->getId())));
        }
        return $this->render('ProtocoloBundle:Reunion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a form to create a Reunion entity.
     *
     * @param Reunion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Reunion $entity)
    {
        $form = $this->createForm(new ReunionType(), $entity, array(
            'action' => $this->generateUrl('reunion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Reunion entity.
     *
     */
    public function newAction()
    {
        $entity = new Reunion();
        $form   = $this->createCreateForm($entity);
        return $this->render('ProtocoloBundle:Reunion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Finds and displays a Reunion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Reunion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reunion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Reunion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reunion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Reunion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reunion entity.');
        }

        $refrigerio = $entity->getRefrigerio();
        $refri = explode(",", $refrigerio);
        $entity->setRefrigerio($refri);
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Reunion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Reunion entity.
    *
    * @param Reunion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Reunion $entity)
    {
        $form = $this->createForm(new ReunionType(), $entity, array(
            'action' => $this->generateUrl('reunion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Reunion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Reunion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reunion entity.');
        }
        $data = $request->request->all();
        $refrigerios = $data['frontend_protocolobundle_reunion']['refrigerio'];
        $entity->setRefrigerio($refrigerios);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        
        if ($editForm->isValid()) {

            $refrigerio = '';
            $refrigerios = array_filter($entity->getRefrigerio(), 'strlen');
            $c = count($refrigerios);
            foreach ($refrigerios as $refri) {
                if ($c > 1) {
                    $refrigerio .= $refri . ',';
                } else {
                    $refrigerio .= $refri;
                }
                --$c;
            }

            $entity->setRefrigerio($refrigerio);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA EDITADO EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('reunion_show', array('id' => $id)));
        }
        $this->get('session')->getFlashBag()->add('alert', 'OCURRIO UN ERROR AL EDITAR LA SOLICITUD..');
        return $this->render('ProtocoloBundle:Reunion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Reunion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProtocoloBundle:Reunion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Reunion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('reunion'));
    }

    /**
     * Creates a form to delete a Reunion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reunion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                    'label' => 'Eliminar',
                    'attr'  => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }
}
