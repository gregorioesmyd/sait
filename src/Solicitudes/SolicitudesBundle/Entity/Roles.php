<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Solicitudes.roles
 *
 * @ORM\Table(name="solicitudes.roles")
 * @ORM\Entity
 */
class Roles
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.roles_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="administradores", type="string", length=255, nullable=false)
     */
    private $administradores;

    /**
     * @var string
     *
     * @ORM\Column(name="consulta", type="string", length=255, nullable=false)
     */
    private $consulta;

    /**
     * @var \Solicitudes
     *
     * @ORM\ManyToOne(targetEntity="Solicitudes\SolicitudesBundle\Entity\Solicitudes", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitudes_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitudes;



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
     * Set administradores
     *
     * @param string $administradores
     * @return Roles
     */
    public function setAdministradores($administradores)
    {
        $this->administradores = $administradores;
    
        return $this;
    }

    /**
     * Get administradores
     *
     * @return string 
     */
    public function getAdministradores()
    {
        return $this->administradores;
    }

    /**
     * Set consulta
     *
     * @param string $consulta
     * @return Roles
     */
    public function setConsulta($consulta)
    {
        $this->consulta = $consulta;
    
        return $this;
    }

    /**
     * Get consulta
     *
     * @return string 
     */
    public function getConsulta()
    {
        return $this->consulta;
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