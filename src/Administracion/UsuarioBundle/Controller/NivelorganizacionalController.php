<?php

namespace Administracion\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Administracion\UsuarioBundle\Entity\Nivelorganizacional;
use Administracion\UsuarioBundle\Form\NivelorganizacionalType;

/**
 * Nivelorganizacional controller.
 *
 */
class NivelorganizacionalController extends Controller
{

    /**
     * Lists all Nivelorganizacional entities.
     *
     */
    
    public function ReporteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "select x from UsuarioBundle:Nivelorganizacional x where x.codigo!='0000' order by x.codigo ASC";
        $query = $em->createQuery($dql);
        $entities = $query->getResult();

        
        $html= $this->render('UsuarioBundle:Nivelorganizacional:reporte.html.twig', array(
            'entities' => $entities,
        ));
        
        include("libs/MPDF/mpdf.php");
        $mpdf=new \mPDF();
        //izq - der - arr - aba
        //$mpdf->AddPage('P','','','','',10,10,0,0);
        $mpdf->AddPage('P','','','','',10,10,10,10,18,12);
        //$stylesheet = file_get_contents('libs/bootstrap3/css/bootstrap.min.css');
        $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text

        $mpdf->WriteHTML($html->getContent());
        $mpdf->Output("nivelesTelesur".".pdf","D");
        die;
        
    }
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UsuarioBundle:Nivelorganizacional')->findAll(array(),array('codigo'=>'ASC'));

        return $this->render('UsuarioBundle:Nivelorganizacional:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Nivelorganizacional entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Nivelorganizacional();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('nivelorganizacional_show', array('id' => $entity->getId())));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Hay un error en el formulario, por favor revise.');
        return $this->render('UsuarioBundle:Nivelorganizacional:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Nivelorganizacional entity.
     *
     * @param Nivelorganizacional $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Nivelorganizacional $entity)
    {
        $form = $this->createForm(new NivelorganizacionalType(), $entity, array(
            'action' => $this->generateUrl('nivelorganizacional_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new Nivelorganizacional entity.
     *
     */
    public function newAction()
    {
        $entity = new Nivelorganizacional();
        $form   = $this->createCreateForm($entity);

        return $this->render('UsuarioBundle:Nivelorganizacional:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Nivelorganizacional entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nivelorganizacional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Nivelorganizacional:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Nivelorganizacional entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nivelorganizacional entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UsuarioBundle:Nivelorganizacional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Nivelorganizacional entity.
    *
    * @param Nivelorganizacional $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Nivelorganizacional $entity)
    {
        $form = $this->createForm(new NivelorganizacionalType(), $entity, array(
            'action' => $this->generateUrl('nivelorganizacional_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    /**
     * Edits an existing Nivelorganizacional entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nivelorganizacional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Nivel editado con exito.');
            return $this->redirect($this->generateUrl('nivelorganizacional_edit', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Hay un error en el formulario, por favor revise.');
        $this->get('session')->getFlashBag()->add('alert', 'Debe cargar una imágen.');
        return $this->render('UsuarioBundle:Nivelorganizacional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Nivelorganizacional entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UsuarioBundle:Nivelorganizacional')->find($id);
            
            $perfiles = $em->getRepository('UsuarioBundle:Perfil')->findByNivelorganizacional($id);

            
            
            if(!empty($perfiles)):
                $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> No puede eliminar este nivel porque se encuentra asociado a una o varias personas.');
                return $this->redirect($this->generateUrl('nivelorganizacional_edit',array('id'=>$id)));
            endif;
            
            
            
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Nivelorganizacional entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('nivelorganizacional'));
    }

    /**
     * Creates a form to delete a Nivelorganizacional entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nivelorganizacional_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
