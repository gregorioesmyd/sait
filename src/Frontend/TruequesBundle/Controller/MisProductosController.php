<?php

namespace Frontend\TruequesBundle\Controller;

use Frontend\TruequesBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Frontend\TruequesBundle\Entity\MisProductos;
use Frontend\TruequesBundle\Entity\Trueque;
use Frontend\TruequesBundle\Form\MisProductosType;

/**
 * MisProductos controller.
 *
 */
class MisProductosController extends BaseController
{

	public function productoAction(Request $request)
	{
		$categoria_id = $request->request->get('categoria_id');

		$em = $this->getDoctrine()->getManager();

		$productos = $em->getRepository('TruequesBundle:Producto')->findByCategoria($categoria_id);

		foreach ($productos as $producto) {
			$data[] = $this->serializeProductos($producto);
		}

		$response = new JsonResponse();

		$response->setData(array(
			'productos' => $data
			));

		return $response;
	}

	public function getMiProductoIdAction(Request $request)
	{
		$id_producto = $request->request->get('id_miproducto');
		
  
		$em = $this->getDoctrine()->getManager();

		$misproductos = $em->getRepository('TruequesBundle:MisProductos')->find($id_producto);

		$data = $this->serializeMisProductos($misproductos);

		$response = new JsonResponse();

		$response->setData(array(
			'productos' => $data
			));

		return $response;
	}

	private function serializeMisProductos($misproductos)
	{
		$data = array(
			'id' => $misproductos->getId(),
			'nombre' => $misproductos->getProductoCambiar()->getNombre()
			);

		foreach ($misproductos->getMisProductoInteres() as $key => $value) {
			$data['intereses'][] =  $value->getNombre();
		}

		return $data;
	}

	private function serializeProductos($producto)
	{
		return array(
			'id' => $producto->getId(),
			'nombre' => $producto->getNombre(),
			);
	}

	/**
	 * Lists all MisProductos entities.
	 *
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();

		$user = $this->findPerfilUser($this->currentUser());

		// if ($this->get('security.context')->isGranted('ROLE_TRUEQUES_ADMIN')) {
		// 	$entities = $em->getRepository('TruequesBundle:MisProductos')->findAll();
		// } else {
			$entities = $em->getRepository('TruequesBundle:MisProductos')->findByUser($user);
		// }

		return $this->render('TruequesBundle:MisProductos:index.html.twig', array(
			'entities' => $entities,
			));
	}
	/**
	 * Creates a new MisProductos entity.
	 *
	 */

	public function createAction(Request $request)
	{
		$valid=array();
		$entity = new MisProductos();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		/*$data=$request->request->all();
		$data=$data['frontend_truequesbundle_misproductos'];
		if($data['categoria']==''):
			$valid[]='Debe seleccionar una categoria.';
		endif;
		if(isset($data['mis_producto_interes']) and $data['mis_producto_interes']=='' or !isset($data['mis_producto_interes'])):
			$valid[]='Debe seleccionar un producto.';
		endif;*/



		if ($form->isValid() and empty($valid)) {

			$em = $this->getDoctrine()->getManager();
			$user = $this->findPerfilUser($this->currentUser());
                        

			$cantidad = $em->getRepository('TruequesBundle:MisProductos')->findCantidadPublicada($user);

			if($cantidad[1]>2):
                            $this->get('session')->getFlashBag()->add('danger', 'No puede tener mas de tres publicaciones en proceso');
                            return $this->redirect($this->generateUrl('misproductos'));
			endif;

			
			$entity->setUser($user);
			$status = $this->getStatus('Activo');
			$entity->setStatus($status);

	        $fc=new \DateTime(\date("d-m-Y G:i:s"));
	        $ffp = strtotime ( '+7 days' , strtotime ( $fc->format('d-m-Y') ) ) ;
	        $ffp=new \DateTime(\date("d-m-Y",$ffp));

	        $entity->setFechaCreacion($fc);
	        $entity->setFechaFinPublicacion($ffp);

			$em->persist($entity);
			$em->flush();

			$this->sendMail('Aplicación Trueques - Bienvenidos', 
				'aplicaciones@telesurtv.net', 
				 $user->getUser()->getUsername().'@telesurtv.net', 
				'Has ingresado y cargado tu solicitud de trueque con éxito. En el momento que alguien se interese por tu(s) producto(s) te lo notificaremos por esta misma vía. <span style="color:red";><br><br>Recuerda que tu solicitud caduca a los 7 días</span>');

			return $this->redirect($this->generateUrl('misproductos_show', array('id' => $entity->getId())));
		}

		$string = var_export($this->getErrorMessages($form), true);

		return $this->render('TruequesBundle:MisProductos:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
			//'valid'=>$valid
			));
	}

	private function getErrorMessages(\Symfony\Component\Form\Form $form) {
		$errors = array();

		foreach ($form->getErrors() as $key => $error) {
			if ($form->isRoot()) {
				$errors['#'][] = $error->getMessage();
			} else {
				$errors[] = $error->getMessage();
			}
		}

		foreach ($form->all() as $child) {
			if (!$child->isValid()) {
				$errors[$child->getName()] = $this->getErrorMessages($child);
			}
		}

		return $errors;
	}

	/**
	 * Creates a form to create a MisProductos entity.
	 *
	 * @param MisProductos $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(MisProductos $entity)
	{
		$form = $this->createForm(new MisProductosType(), $entity, array(
			'action' => $this->generateUrl('misproductos_create'),
			'method' => 'POST',
			));

		//$form->add('submit', 'submit', array('label' => 'Create'));

		return $form;
	}

	/**
	 * Displays a form to create a new MisProductos entity.
	 *
	 */
	public function newAction()
	{
		$entity = new MisProductos();
		$form   = $this->createCreateForm($entity);

		return $this->render('TruequesBundle:MisProductos:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
			));
	}

	/**
	 * Finds and displays a MisProductos entity.
	 *
	 */
	public function showAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$user = $this->findPerfilUser($this->currentUser());

		$entity = $em->getRepository('TruequesBundle:MisProductos')->findOneBy(array(
			'id' => $id,
			'user' => $user
			));

		if (!$entity) 
		{
			$request->getSession()->getFlashBag()->add(
				'danger',
				'El producto que busca no esta registrado!'
				);

			return $this->redirect($this->generateUrl('trueques_homepage'));
		}

		$deleteForm = $this->createDeleteForm($id);

		$parameters = array(
			'entity'      => $entity,
			'delete_form' => $deleteForm->createView(),
		);

		if ($entity->getStatus() == 'Confirmado') {
			$trueque = new Trueque();
			$trueque = $em->getRepository('TruequesBundle:Trueque')->findProductsTrueque($entity);

			if (!$trueque) {
				print_r('passsss');
			}

			if ($trueque) {
				$mis_producto_user1 = $trueque->getMisproductoUser1();
				$mis_producto_user2 = $trueque->getMisproductoUser2();
				$parameters['mis_producto_user1'] = $mis_producto_user1;
				$parameters['mis_producto_user2'] = $mis_producto_user2;
				$parameters['trueque'] = $trueque;
			}
		}

		return $this->render('TruequesBundle:MisProductos:show.html.twig', $parameters);
	}

	/**
	 * Displays a form to edit an existing MisProductos entity.
	 *
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('TruequesBundle:MisProductos')->find($id);
                
                if($entity->getStatus()!='Activo' and $entity->getStatus()!='Cancelado'){
                    $this->get('session')->getFlashBag()->add('danger', 'No puede editar este producto');
                    return $this->redirect($this->generateUrl('misproductos'));
                }
                

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find MisProductos entity.');
		}

		$editForm = $this->createEditForm($entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->render('TruequesBundle:MisProductos:edit.html.twig', array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			));
	}

	/**
	* Creates a form to edit a MisProductos entity.
	*
	* @param MisProductos $entity The entity
	*
	* @return \Symfony\Component\Form\Form The form
	*/
	private function createEditForm(MisProductos $entity)
	{
		$form = $this->createForm(new MisProductosType(), $entity, array(
			'action' => $this->generateUrl('misproductos_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			));

		//$form->add('submit', 'submit', array('label' => 'Update'));

		return $form;
	}
	/**
	 * Edits an existing MisProductos entity.
	 *
	 */
	public function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('TruequesBundle:MisProductos')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find MisProductos entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();

			return $this->redirect($this->generateUrl('misproductos_show', array('id' => $id)));
		}

		return $this->render('TruequesBundle:MisProductos:edit.html.twig', array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			));
	}
	/**
	 * Deletes a MisProductos entity.
	 *
	 */
	public function deleteAction(Request $request, $id)
	{
		$form = $this->createDeleteForm($id);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('TruequesBundle:MisProductos')->find($id);

			if (!$entity) {
				throw $this->createNotFoundException('Unable to find MisProductos entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('misproductos'));
	}

	/**
	 * Creates a form to delete a MisProductos entity by id.
	 *
	 * @param mixed $id The entity id
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm($id)
	{
		return $this->createFormBuilder()
		->setAction($this->generateUrl('misproductos_delete', array('id' => $id)))
		->setMethod('DELETE')
		->add('submit', 'submit', array('label' => 'Eliminar'))
		->getForm()
		;
	}


	/**
	 * Lists all MisProductos entities.
	 *
	 */
	public function aprobarAction()
	{
		$em = $this->getDoctrine()->getManager();

		$user = $this->findPerfilUser($this->currentUser());

		$entities = $em->getRepository('TruequesBundle:MisProductos')->findAll();

		return $this->render('TruequesBundle:MisProductos:aprobar.html.twig', array(
			'entities' => $entities,
			));
	}

	public function aprobarAsyncAction(Request $request)
	{
		$misproducto_id = $request->request->get('id');
		$status = $request->request->get('status');
		$response_status = "fail";

		if ($status == 'aprobar' or $status == 'rechazar') {
			$em = $this->getDoctrine()->getManager();
			$misproductos = $em->getRepository('TruequesBundle:MisProductos')->find($misproducto_id);
			if ($misproductos) 
			{
				$status_text = $status == 'aprobar' ? 'Aprobado':'Rechazado';
				$misproductos->setStatus($status_text);
				$em->persist($misproductos);
				$em->flush();
				$response_status = $status;
			}
		}
		
		$response = new JsonResponse();

		$response->setData(array(
			'status' => $response_status
		));

		return $response;
	}
}
