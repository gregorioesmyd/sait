<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.trabajoactividad")
 * @ORM\Entity
 */


class TrabajoActividad extends TrabajoRealizado
{


    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\ProcesarActividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id")
     * })
     */
    private $actividad;

    
   

    /**
     * Set actividad
     *
     * @param \Progis\ProyectoBundle\Entity\Actividad $actividad
     * @return TrabajoActividad
     */
    public function setActividad(\Progis\ProyectoBundle\Entity\ProcesarActividad $actividad = null)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad
     *
     * @return \Progis\ProyectoBundle\Entity\Actividad 
     */
    public function getActividad()
    {
        return $this->actividad;
    }
}