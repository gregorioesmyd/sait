<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitudes.responsables
 *
 * @ORM\Table(name="solicitudes.responsables")
 * @ORM\Entity
 */
class Responsables
{
    /** 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.responsables_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
  

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $responsable;


    /**
     * @var \Solicitudes
     *
     * @ORM\ManyToOne(targetEntity="Solicitudes\SolicitudesBundle\Entity\Solicitudes", inversedBy="responsables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitudes_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitudes;

    public function __toString()
    {
        return $this->responsable;
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
     * Set responsable
     *
     * @param string $responsable
     * @return Responsables
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;
    
        return $this;
    }

    /**
     * Get responsable
     *
     * @return string 
     */
    public function getResponsable()
    {
        return $this->responsable;
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