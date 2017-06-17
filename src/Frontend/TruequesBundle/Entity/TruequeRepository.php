<?php

namespace Frontend\TruequesBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Frontend\TruequesBundle\Entity\MisProductos;

class TruequeRepository extends EntityRepository
{
    
    public function findStatusTrueque(MisProductos $misProductos, $status){

        $query = $this->getEntityManager()->createQuery("
            SELECT t
            FROM TruequesBundle:Trueque t
            WHERE (t.mis_producto_user1 = :misProductos1 OR t.mis_producto_user2 = :misProductos2)
            AND t.status = :status
        ")->setParameters(array(
        		'misProductos1' => $misProductos->getId(),
        		'misProductos2' => $misProductos->getId(),
        		'status' => $status
        	));

        return $query->getOneOrNullResult();
    }

    public function findProductsTrueque(MisProductos $misProductos){

        $query = $this->getEntityManager()->createQuery("
            SELECT t
            FROM TruequesBundle:Trueque t
            WHERE (t.mis_producto_user1 = :misProductos1 OR t.mis_producto_user2 = :misProductos2)
        ")->setParameters(array(
                'misProductos1' => $misProductos->getId(),
                'misProductos2' => $misProductos->getId(),
            ));

        return $query->getOneOrNullResult();
    }
}