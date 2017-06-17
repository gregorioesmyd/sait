<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitudes.requerimientos
 *
 * @ORM\Table(name="solicitudes.requerimientos")
 * @ORM\Entity
 */
class Requerimientos
{
    /** 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.requerimientos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="requerimiento", type="text", nullable=false)
     */
    private $requerimiento;

    /**
     * @var \Solicitudes
     *
     * @ORM\ManyToOne(targetEntity="Solicitudes\SolicitudesBundle\Entity\Solicitudes", inversedBy="requerimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitudes_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitudes;

    public function __toString()
    {
        return $this->requerimiento;
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
     * Set requerimiento
     *
     * @param string $requerimiento
     * @return Requerimientos
     */
    public function setRequerimiento($requerimiento)
    {
        $this->requerimiento = $requerimiento;
    
        return $this;
    }

    /**
     * Get requerimiento
     *
     * @return string 
     */
    public function getRequerimiento()
    {
        return $this->requerimiento;
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