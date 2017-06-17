<?php

namespace Frontend\TruequesBundle\Controller;

use Frontend\TruequesBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Frontend\TruequesBundle\Entity\MisProductos;
use Frontend\TruequesBundle\Entity\Categoria;
use Frontend\TruequesBundle\Entity\Trueque;

class DefaultController extends BaseController
{
    public function indexAction()
    {
        return $this->render('TruequesBundle:Default:index.html.twig');
    }

    public function searchProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->findPerfilUser($this->currentUser());

        $status = $this->getStatus('Activo');

        $misproductos = $em->getRepository('TruequesBundle:MisProductos')->findBy(array(
                    'user' => $user, 
                    'status' => $status
            ));

        if (!$misproductos) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Usted no tiene ningun producto cargado o activo aun!'
            );

            return $this->redirect($this->generateUrl('trueques_homepage'));
        }

        $categoria = $em->getRepository('TruequesBundle:Categoria')->findOneByNombre('Alimentos');

        $categories = $em->getRepository('TruequesBundle:Categoria')->findAll();

        $entities = $em->getRepository('TruequesBundle:MisProductos')->findAvailableCategoryExcludeUser($user, $categoria, $status);


        return $this->render('TruequesBundle:Default:buscar_productos.html.twig', array(
                'entities' => $entities,
                'categories' => $categories
            ));
    }

    public function listProductAction()
    {
        return $this->render('TruequesBundle:Default:index.html.twig');
    }

    public function getMisProductosCategoriaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categoria_id = $request->request->get('categoria');

        $categoria = new Categoria();
        $categoria = $em->getRepository('TruequesBundle:Categoria')->find($categoria_id);

        $user = $this->findPerfilUser($this->currentUser());

        $status = $this->getStatus('Activo');

        $entities = $em->getRepository('TruequesBundle:MisProductos')->findAvailableCategoryExcludeUser($user, $categoria, $status);

        $data = [];
        foreach ($entities as $misproductos) {
            $data[] = $this->serializeMisProductos($misproductos);
        }

        $response = new JsonResponse();

        $response->setData(array(
            'entities' => $data
        ));

        return $response;
    }

    private function serializeMisProductos($misproductos)
    {
        return array(
            'id' => $misproductos->getId(),
            'productoCambiar' => $misproductos->getProductoCambiar()->getNombre(),
        );
    }


    public function contactarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $misproducto_id_check = $request->request->get('miproducto');

        $miproducto_check = $em->getRepository('TruequesBundle:MisProductos')->find($misproducto_id_check);

        $user = $this->findPerfilUser($this->currentUser());
        $misproducto_user = $em->getRepository('TruequesBundle:MisProductos')->findAvailableUser($user);

        $responseData = $this->reglasIntercambio($miproducto_check, $misproducto_user);

        $status = "fail";
        $data = array();
        if (count($responseData) > 0 ) {
            $status = "success";
            foreach ($responseData as $key => $misproductos) {
                $status = $this->getStatus('Contacto');
                $misproductos->setStatus($status);
                $em->persist($misproductos);
                $miproducto_check->addInteresado($misproductos);
                $data[] = $this->serializeMisProductos($misproductos);
            }
            try {  
                $miproducto_check->setStatus($status);
                $em->persist($miproducto_check);
                $em->flush();

                $this->sendMail('Aplicación Trueques - Solicitud de interesados', 
                        'aplicaciones@telesurtv.net', 
                        $miproducto_check->getUser()->getUser()->getUsername().'@telesurtv.net', 
                        'Alguien está interesado en tu(s) producto(s) ingresa a la aplicación, 
                         en la sección mis productos, y ponte en contacto con esa persona.');

            } catch (\Doctrine\DBAL\DBALException $e) {
                $status = "uniqueFail";
            }
        }        
        
        $response = new JsonResponse();

        $response->setData(array(
            'entities' => $data,
            'status' => $status
        ));

        return $response;
    }

    private function reglasIntercambio($miproducto_check, $misproducto_user)
    {
        /**
         * Verificar que el usuario que busca tengo un producto cargado en el cual el usuario que tiene el producto
         * este interesado
         */
        $coincidenciaProductos = [];
        foreach ($miproducto_check->getMisProductoInteres() as $key => $objectProductos) {
            foreach ($misproducto_user as $key => $value) {
                if ($objectProductos->getId() == $value->getProductoCambiar()->getId() ) {
                    $coincidenciaProductos[] = $value;
                }
            }
        }

        /**
         * Verificar que el usuario que busca haya cargado un producto y tenga en sus intereses el producto que ofrece el otro usuario
         */
        $listProductos  = [];
        foreach ($coincidenciaProductos as $key => $objects) {
            foreach ($objects->getMisProductoInteres() as $key => $value) {
                if ($value->getId() == $miproducto_check->getProductoCambiar()->getId()) {
                    $listProductos[] = $objects;
                    break;
                }
            }
        }

        return $listProductos;
    }


    public function reservarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mi_producto = $request->request->get('mi_producto');
        $producto_trueque = $request->request->get('producto_trueque');

        $mi_producto = $em->getRepository('TruequesBundle:MisProductos')->find($mi_producto);
        $producto_trueque = $em->getRepository('TruequesBundle:MisProductos')->find($producto_trueque);

        $trueque = new Trueque();

        $trueque->setMisproductoUser1($mi_producto);
        $trueque->setMisproductoUser2($producto_trueque);

        $status = $this->getStatus('Confirmado');
        $mi_producto->setStatus($status);
        $producto_trueque->setStatus($status);
        $trueque->setStatusUser1($status);
        $trueque->setStatusUser2($status);

        $status = "success";
        try {  
            $em->persist($trueque);
            $em->persist($mi_producto);
            $em->persist($producto_trueque);
            $em->flush();

            $msg =  $producto_trueque->getUser()->getPrimerNombre() . ' ' . $producto_trueque->getUser()->getPrimerApellido() . 
                    " está interesado en tu producto colocate en contacto con él. Ext:" .  $producto_trueque->getUser()->getExtension() . 
                    " o al correo " . $producto_trueque->getUser()->getUser()->getUsername() . "@telesurtv.net";
                    
            $this->sendMail('Aplicación Trueques - Inicio de confirmacion de trueques', 
                'aplicaciones@telesurtv.net', 
                $mi_producto->getUser()->getUser()->getUsername().'@telesurtv.net', 
                $msg
            );

        } catch (\Doctrine\DBAL\DBALException $e) {
            $status = "uniqueFail";
        }
        
        $response = new JsonResponse();

        $response->setData(array(
            'status' => $status
        ));

        return $response;
    }

    public function finalizarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id_trueque = $request->request->get('id_trueque');

        $trueque = $em->getRepository('TruequesBundle:Trueque')->find($id_trueque);

        $mi_producto_1 = $em->getRepository('TruequesBundle:MisProductos')->find($trueque->getMisProductoUser1()->getId());
        $mi_producto_2 = $em->getRepository('TruequesBundle:MisProductos')->find($trueque->getMisProductoUser2()->getId());


        $status = $this->getStatus('Exitoso');
        //$status_producto = $this->getStatus('Inactivo');
        $trueque->setStatusUser1($status);
        $trueque->setStatusUser2($status);
        $mi_producto_1->setStatus($status);
        $mi_producto_2->setStatus($status);

        $em->persist($trueque);
        $em->persist($mi_producto_1);
        $em->persist($mi_producto_2);
        $em->flush();

        $this->sendMail('Aplicación Trueques - Proceso de trueques finalizado', 
                'aplicaciones@telesurtv.net', 
                array($mi_producto_1->getUser()->getUser()->getUsername().'@telesurtv.net',$mi_producto_2->getUser()->getUser()->getUsername().'@telesurtv.net'), 
                'El usuario '.$mi_producto_1->getUser()->getPrimerNombre().' '.$mi_producto_1->getUser()->getPrimerApellido().' ha notificado que el intercambio se ha realizado exitosamente.');

        $response = new JsonResponse();

        $response->setData(array(
            'status' => "success"
        ));

        return $response;
    }

    public function cancelarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id_trueque = $request->request->get('id_trueque');

        $trueque = $em->getRepository('TruequesBundle:Trueque')->find($id_trueque);

        $mi_producto_1 = $em->getRepository('TruequesBundle:MisProductos')->find($trueque->getMisProductoUser1()->getId());
        $mi_producto_2 = $em->getRepository('TruequesBundle:MisProductos')->find($trueque->getMisProductoUser2()->getId());


        $status = $this->getStatus('Cancelado');
        $status_producto = $this->getStatus('Activo');
        /*$trueque->setStatusUser1($status);
        $trueque->setStatusUser2($status);*/
        $mi_producto_1->setStatus($status_producto);
        $mi_producto_2->setStatus($status_producto);

        $mi_producto_1->removeInteresado($mi_producto_2);

        $em->persist($trueque);
        $em->persist($mi_producto_1);
        $em->persist($mi_producto_2);
        $em->flush();

        $em->remove($trueque);
        $em->flush();

       /* $em->remove($entity);
        $em->flush();*/

        $user = $this->findPerfilUser($this->currentUser());

        $this->sendMail('Aplicación Trueques - Proceso de trueques cancelado', 
                'aplicaciones@telesurtv.net', 
                array($mi_producto_1->getUser()->getUser()->getUsername().'@telesurtv.net',$mi_producto_2->getUser()->getUser()->getUsername().'@telesurtv.net'), 
                'El usuario '.ucfirst($user->getPrimerNombre()).' '.ucfirst($user->getPrimerApellido()).' ha cancelado el trueque.');

        $response = new JsonResponse();

        $response->setData(array(
            'status' => "success"
        ));

        return $response;
    }

}
