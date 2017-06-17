<?php

namespace Frontend\FormularioBundle\Controller\Bazar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\FormularioBundle\Entity\Bazar\Postular;
use Frontend\FormularioBundle\Form\Bazar\PostularType;

/**
 * Bazar\Postular controller.
 *
 */
class PostularController extends Controller
{

    /**
     * Lists all Bazar\Postular entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FormularioBundle:Bazar\Postular')->findAll();

        return $this->render('FormularioBundle:Bazar/Postular:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Bazar\Postular entity.
     *
     */
    public function createAction(Request $request)
    {
        $data=$request->request->all();
        if(isset($data['frontend_formulariobundle_bazar_postular']['producto'])):
            $productoSeleccionado=$data['frontend_formulariobundle_bazar_postular']['producto'];

            $validaOtro=0;
            foreach ($productoSeleccionado as $ps) {
                if($ps==9)$validaOtro=1;
            }
        else: $validaOtro=0;
        
        endif;

        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new Postular();
        $form = $this->createCreateForm($entity,$validaOtro);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setSolicitante($perfil);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Prostulación enviada exitosamente.');
            return $this->redirect($this->generateUrl('bazar_postular_show', array('id' => $entity->getId())));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('FormularioBundle:Bazar/Postular:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
     * Creates a form to create a Bazar\Postular entity.
     *
     * @param Postular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Postular $entity,$validaOtro,$em)
    {
        if($validaOtro==1):
            
            $form = $this->createForm(new PostularType($em,$entity->getProducto()), $entity, array(
                'action' => $this->generateUrl('bazar_postular_create'),
                'method' => 'POST',
                'validation_groups' => array('descripcionOtro','Default'),
            ));
      
        else:
            $form = $this->createForm(new PostularType($em,$entity->getProducto()), $entity, array(
                'action' => $this->generateUrl('bazar_postular_create'),
                'method' => 'POST',
                'validation_groups' => array('Default'),
            ));
        endif;


        return $form;
    }

    /**
     * Displays a form to create a new Bazar\Postular entity.
     *
     */
    public function newAction()
    {
        /*if(\date("Y-m-d")>"2015-10-23"){
            $this->get('session')->getFlashBag()->add('alert', 'Las postulaciones estan cerradas.');
            return $this->redirect($this->generateUrl('usuario_homepage'));
        }*/
        $em = $this->getDoctrine()->getManager();
        $entity = new Postular();
        $form   = $this->createCreateForm($entity,0,$em);
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        
        $postular =  $em->getRepository('FormularioBundle:Bazar\Postular')->findBySolicitante($idusuario);

        if(!empty($postular)):
            $this->get('session')->getFlashBag()->add('notice', 'Ya posee una postulación.');
            return $this->redirect($this->generateUrl('bazar_postular_show', array('id' => $postular[0]->getId())));
        else:
            $this->get('session')->getFlashBag()->add('alert', 'Usted no está inscrito para la Feria Navideña.');
            return $this->redirect($this->generateUrl('usuario_homepage'));
        endif;
        
        
        return $this->render('FormularioBundle:Bazar/Postular:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
     * Finds and displays a Bazar\Postular entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $entity = $em->getRepository('FormularioBundle:Bazar\Postular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bazar\Postular entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FormularioBundle:Bazar/Postular:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
     * Displays a form to edit an existing Bazar\Postular entity.
     *
     */
    public function editAction($id)
    {

        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = $em->getRepository('FormularioBundle:Bazar\Postular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bazar\Postular entity.');
        }
        
        $editForm = $this->createEditForm($entity,$em,$entity->getTipoProducto());
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FormularioBundle:Bazar/Postular:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'perfil'=>$perfil
        ));
    }

    /**
    * Creates a form to edit a Bazar\Postular entity.
    *
    * @param Postular $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Postular $entity,$em,$tipo)
    {
        if($tipo=='g'):
            $form = $this->createForm(new PostularType($em,$entity->getProducto()), $entity, array(
                'action' => $this->generateUrl('bazar_postular_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'validation_groups' => array('Default','Gastronomia'),
            ));
        else:
            $form = $this->createForm(new PostularType($em,$entity->getProducto()), $entity, array(
                'action' => $this->generateUrl('bazar_postular_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'validation_groups' => array('Default','Otros'),
            ));
        endif;
        return $form;
    }
    /**
     * Edits an existing Bazar\Postular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $data=$request->request->all();
        $data=$data['frontend_formulariobundle_bazar_postular'];
        
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = $em->getRepository('FormularioBundle:Bazar\Postular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bazar\Postular entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity,$em,$data['tipoProducto']);
        
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            

            
            if($data['tipoProducto']=='o'):
                    
                //borro todos los de gastronomia
                $producto = $em->getRepository('FormularioBundle:Bazar\Producto')->findAll(array('comida'=>true));
                foreach ($producto as $p) {
                    $entity->removeProducto($p);
                }

                foreach ($data['productoOtros'] as $po){

                    $producto = $em->getRepository('FormularioBundle:Bazar\Producto')->find($po);
                    $entity->addProducto($producto);
                }
                
            elseif($data['tipoProducto']=='g'):
                
                //borro todos los de manualidades
                $producto = $em->getRepository('FormularioBundle:Bazar\Producto')->findAll(array('comida'=>false));
                foreach ($producto as $p) {
                    $entity->removeProducto($p);
                }
                
                foreach ($data['productoGastronomia'] as $po){

                    $producto = $em->getRepository('FormularioBundle:Bazar\Producto')->find($po);
                    $entity->addProducto($producto);

                }
                
            endif;

            $entity->setSolicitante($perfil);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Prostulación editada exitosamente.');
            return $this->redirect($this->generateUrl('bazar_postular_show', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta</b>! Ha ocurrido un error en el formulario.');
        return $this->render('FormularioBundle:Bazar/Postular:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'perfil'=>$perfil
        ));
    }
    /**
     * Deletes a Bazar\Postular entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FormularioBundle:Bazar\Postular')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Bazar\Postular entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

            $this->get('session')->getFlashBag()->add('notice', 'Prostulación borrada exitosamente.');
            return $this->redirect($this->generateUrl('bazar_postular_new'));
    }

    /**
     * Creates a form to delete a Bazar\Postular entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bazar_postular_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
