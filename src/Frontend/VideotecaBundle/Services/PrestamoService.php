<?php

namespace Frontend\VideotecaBundle\Services;


/**
* Description of PrestamoService
*
* @author ecastro
*/
class PrestamoService {

    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function countPrestamoUser($user)
    {
        $dql = "select COUNT(a) from VideotecaBundle:TmpPrestamo a where a.prestamista=:prestamista ";
        $query = $this->em->createQuery($dql);
        $query->setParameter('prestamista', $user);
        return $query->getSingleScalarResult();
    }

}
