<?php

namespace Frontend\TransporteBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AsignacionesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AsignacionesRepository extends EntityRepository
{
	 public function SolicitudAsignada($idsolicitud)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT a FROM TransporteBundle:Asignaciones a WHERE
        a.idSolicitud = :idsol');
        $consulta->setParameter('idsol', $idsolicitud);
        return $consulta->getResult();

    }
}
