<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.metaActividad")
 * @ORM\Entity
 */
class MetaActividad
{
    
    /**
     * @var \Frontend\PrincipalBundle\Entity\Ticket
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\ProcesarActividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id")
     * })
     */
    private $actividad;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="meta_id", referencedColumnName="id")
     * })
     */
    private $meta;
    


    /**
     * Set actividad
     *
     * @param \Progis\ProyectoBundle\Entity\ProcesarActividad $actividad
     * @return MetaActividad
     */
    public function setActividad(\Progis\ProyectoBundle\Entity\ProcesarActividad $actividad)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad
     *
     * @return \Progis\ProyectoBundle\Entity\ProcesarActividad 
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set meta
     *
     * @param \Progis\PrincipalBundle\Entity\Metas $meta
     * @return MetaActividad
     */
    public function setMeta(\Progis\PrincipalBundle\Entity\Metas $meta)
    {
        $this->meta = $meta;
    
        return $this;
    }

    /**
     * Get meta
     *
     * @return \Progis\PrincipalBundle\Entity\Metas 
     */
    public function getMeta()
    {
        return $this->meta;
    }
}