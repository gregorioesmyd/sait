<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitudes.unidades
 *
 * @ORM\Table(name="solicitudes.unidades")
 * @ORM\Entity
 */
class Unidades
{
    /** 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.unidades_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
  
     /**
     * @var \Unidades
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $unidad;

    /**
     * @var \Solicitudes
     *
     * @ORM\ManyToOne(targetEntity="Solicitudes\SolicitudesBundle\Entity\Solicitudes", inversedBy="unidades")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitudes_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitudes;

    public function __toString()
    {
        return $this->unidad;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     * @return Unidades
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad
     *
     * @return string 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set solicitudes
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Solicitudes $solicitudes
     * @return Solicitudes
     */
    public function setSolicitudes(\Solicitudes\SolicitudesBundle\Entity\Solicitudes $solicitudes)
    {
        $this->solicitudes = $solicitudes;
    
        return $this;
    }

    /**
     * Get solicitudes
     *
     * @return \Solicitudes\SolicitudesBundle\Entity\Solicitudes 
     */
    public function getSolicitudes()
    {
        return $this->solicitudes;
    }



}