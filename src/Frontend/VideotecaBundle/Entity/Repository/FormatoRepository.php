<?php

namespace Frontend\VideotecaBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class FormatoRepository extends EntityRepository
{
    public function filterDQL()
    {
        $dql = "SELECT s, c FROM VideotecaBundle:WrapperSegmento\Segmento s 
                    JOIN s.cinta c
                    JOIN c.tipoCinta t
                    JOIN c.formato f
                    JOIN c.marca m
                    JOIN c.duracion d
                    JOIN c.documentalista docu
                    WHERE t.nombre LIKE :tipoCinta 
                        AND c.codigo LIKE :codigo
                        AND f.descripcionFormato like :formato
                        AND m.descripcionMarca like :marca
                        AND d.nombre like :duracion
                        AND c.observaciones like :observacion
                        AND docu.primerNombre like :documentalista
                ";

        return $dql;
    }
}