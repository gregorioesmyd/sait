<?php

namespace Frontend\TruequesBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Administracion\UsuarioBundle\Entity\Perfil;
use Frontend\TruequesBundle\Entity\Categoria;
use Frontend\TruequesBundle\Entity\Status;

class MisProductosRepository extends EntityRepository
{


    public function findCantidadPublicada(Perfil $user){

        $query = $this->getEntityManager()->createQuery("
            SELECT count(MP)
            FROM TruequesBundle:MisProductos MP join MP.status E
            where (E.nombre='Activo' or E.nombre='Contacto' or E.nombre='Confirmado') and MP.user= :user

        ")->setParameter('user', $user->getId());;

        return $query->getSingleResult();
    }
    
    public function findAvailableUser(Perfil $user){

        $query = $this->getEntityManager()->createQuery("
            SELECT MisProductos
            FROM TruequesBundle:MisProductos MisProductos
            LEFT JOIN MisProductos.user user
            WHERE user.id = :user
        ")->setParameter('user', $user->getId());

        return $query->getResult();
    }

    public function findAvailableExcludeUser(Perfil $user){

        $query = $this->getEntityManager()->createQuery("
            SELECT MisProductos
            FROM TruequesBundle:MisProductos MisProductos
            LEFT JOIN MisProductos.user user
            WHERE user.id <> :user
        ")->setParameter('user', $user->getId());

        return $query->getResult();
    }

    public function findAvailableCategoryExcludeUser(Perfil $user, Categoria $categoria, Status $status){

        $query = $this->getEntityManager()->createQuery("
            SELECT MisProductos
            FROM TruequesBundle:MisProductos MisProductos
            LEFT JOIN MisProductos.user user
            LEFT JOIN MisProductos.productoCambiar producto
            WHERE user.id <> :user
            AND producto.categoria = :categoria
            AND MisProductos.status = :status
        ")->setParameters(array(
                'user' => $user->getId(),
                'categoria' => $categoria->getId(),
                'status' => $status
            ));

        return $query->getResult();
    }
}