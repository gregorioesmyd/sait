<?php

namespace Progis\SolicitudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Solicitud
{
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechasolicitud", type="date", nullable=false)
     */
    private $fechasolicitud;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horasolicitud", type="time", nullable=false)
     */
    private $horasolicitud;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechasolucion", type="date", nullable=true)
     */
    private $fechasolucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horasolucion", type="time", nullable=true)
     */
    private $horasolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud", type="string", length=1000, nullable=false)
     * @Assert\NotBlank(message="Debe escribir la solicitud.")
     * @Assert\Length(
     *      min = "10",
     *      max = "1000",
     *      minMessage = "La solicitud no puede se menor a {{ limit }} caracteres de largo.",
     *      maxMessage = "La solicitud no puede se mayor a {{ limit }} caracteres de largo."
     * )
     * )
     */
    private $solicitud;

    /**
     * @var string
     *
     * @ORM\Column(name="solucion", type="text", nullable=true)
     */
    private $solucion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reasignado", type="boolean", nullable=false)
     */
    private $reasignado=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus=1;

    /**
     * @var \Nivelorganizacional
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un nivelorganizacional.")
     */
    private $nivelorganizacional;
    
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
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Operador
     */
    public function setSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $solicitante = null)
    {
        $this->solicitante = $solicitante;
    
        return $this;
    }

    /**
     * Get solicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getSolicitante()
    {
        return $this->solicitante;
    }

    /**
     * Set fechasolicitud
     *
     * @param \DateTime $fechasolicitud
     * @return Solicitud
     */
    public function setFechasolicitud($fechasolicitud)
    {
        $this->fechasolicitud = $fechasolicitud;
    
        return $this;
    }

    /**
     * Get fechasolicitud
     *
     * @return \DateTime 
     */
    public function getFechasolicitud()
    {
        return $this->fechasolicitud;
    }

    /**
     * Set horasolicitud
     *
     * @param \DateTime $horasolicitud
     * @return Solicitud
     */
    public function setHorasolicitud($horasolicitud)
    {
        $this->horasolicitud = $horasolicitud;
    
        return $this;
    }

    /**
     * Get horasolicitud
     *
     * @return \DateTime 
     */
    public function getHorasolicitud()
    {
        return $this->horasolicitud;
    }

    /**
     * Set fechasolucion
     *
     * @param \DateTime $fechasolucion
     * @return Solicitud
     */
    public function setFechasolucion($fechasolucion)
    {
        $this->fechasolucion = $fechasolucion;
    
        return $this;
    }

    /**
     * Get fechasolucion
     *
     * @return \DateTime 
     */
    public function getFechasolucion()
    {
        return $this->fechasolucion;
    }

    /**
     * Set horasolucion
     *
     * @param \DateTime $horasolucion
     * @return Solicitud
     */
    public function setHorasolucion($horasolucion)
    {
        $this->horasolucion = $horasolucion;
    
        return $this;
    }

    /**
     * Get horasolucion
     *
     * @return \DateTime 
     */
    public function getHorasolucion()
    {
        return $this->horasolucion;
    }

    /**
     * Set solicitud
     *
     * @param string $solicitud
     * @return Solicitud
     */
    public function setSolicitud($solicitud)
    {
        $this->solicitud = $solicitud;
    
        return $this;
    }

    /**
     * Get solicitud
     *
     * @return string 
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set solucion
     *
     * @param string $solucion
     * @return Solicitud
     */
    public function setSolucion($solucion)
    {
        $this->solucion = $solucion;
    
        return $this;
    }

    /**
     * Get solucion
     *
     * @return string 
     */
    public function getSolucion()
    {
        return $this->solucion;
    }

    /**
     * Set reasignado
     *
     * @param boolean $reasignado
     * @return Solicitud
     */
    public function setReasignado($reasignado)
    {
        $this->reasignado = $reasignado;
    
        return $this;
    }

    /**
     * Get reasignado
     *
     * @return boolean 
     */
    public function getReasignado()
    {
        return $this->reasignado;
    }

    /**
     * Set estatus
     *
     * @param string $estatus
     * @return Solicitud
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return string 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set nivelorganizacional
     *
     * @param \Progis\SolicitudBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return Solicitud
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional = null)
    {
        $this->nivelorganizacional = $nivelorganizacional;
    
        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Progis\SolicitudBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }
}

