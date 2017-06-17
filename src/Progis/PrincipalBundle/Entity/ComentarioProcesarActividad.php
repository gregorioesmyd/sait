<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.comentarioProcesarActividad")
 * @ORM\Entity
 */
class ComentarioProcesarActividad
{
    
    /**
     * @var \Frontend\ProyectoBundle\Entity\ProcesarActividad
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\ProcesarActividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="procesaractividad_id", referencedColumnName="id")
     * })
     */
    private $procesaractividad;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Comentarioarchivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comentarioarchivo_id", referencedColumnName="id")
     * })
     */
    private $comentarioarchivo;
    

     

    /**
     * Set procesaractividad
     *
     * @param \Progis\ProcesaractividadBundle\Entity\Procesaractividad $procesaractividad
     * @return ComentarioProcesaractividad
     */
    public function setProcesaractividad(\Progis\ProyectoBundle\Entity\ProcesarActividad $procesaractividad)
    {
        $this->procesaractividad = $procesaractividad;
    
        return $this;
    }

    /**
     * Get procesaractividad
     *
     * @return \Progis\ProcesaractividadBundle\Entity\Procesaractividad 
     */
    public function getProcesaractividad()
    {
        return $this->procesaractividad;
    }

    /**
     * Set comentarioarchivo
     *
     * @param \Progis\PrincipalBundle\Entity\Comentarioarchivo $comentarioarchivo
     * @return ComentarioProyecto
     */
    public function setComentarioarchivo(\Progis\PrincipalBundle\Entity\Comentarioarchivo $comentarioarchivo)
    {
        $this->comentarioarchivo = $comentarioarchivo;
    
        return $this;
    }

    /**
     * Get comentarioarchivo
     *
     * @return \Progis\PrincipalBundle\Entity\Comentarioarchivo 
     */
    public function getComentarioarchivo()
    {
        return $this->comentarioarchivo;
    }
}