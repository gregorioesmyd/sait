<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.comentarioproyecto")
 * @ORM\Entity
 */
class ComentarioProyecto
{
    
    /**
     * @var \Frontend\ProyectoBundle\Entity\Proyecto
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\Proyecto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")
     * })
     */
    private $proyecto;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Comentarioarchivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comentarioarchivo_id", referencedColumnName="id")
     * })
     */
    private $comentarioarchivo;
    

     

    /**
     * Set proyecto
     *
     * @param \Progis\ProyectoBundle\Entity\Proyecto $proyecto
     * @return ComentarioProyecto
     */
    public function setProyecto(\Progis\ProyectoBundle\Entity\Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    
        return $this;
    }

    /**
     * Get proyecto
     *
     * @return \Progis\ProyectoBundle\Entity\Proyecto 
     */
    public function getProyecto()
    {
        return $this->proyecto;
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