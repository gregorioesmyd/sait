<?php

namespace Progis\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proyecto
 *
 * @ORM\Table(name="progis.proyecto")
 * @ORM\Entity
 */
class Proyecto
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank(message="El nombre del proyecto no puede estar en blanco.")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="La descripcion del proyecto no puede estar en blanco.")
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicioestimada", type="date", nullable=true)
     */
    private $fechainicioestimada;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicioreal", type="date", nullable=true)
     */
    private $fechainicioreal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafinestimada", type="date", nullable=true)
     */
    private $fechafinestimada;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafinreal", type="date", nullable=true)
     */
    private $fechafinreal;
    
    /**
     * @var integer
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */

    private $estatus;


    /**
     * @var integer
     *
     * @ORM\Column(name="porcentaje", type="integer", nullable=false)
     */
    
    private $porcentaje;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="informegestion", type="boolean", nullable=false)
     */
    
    private $informegestion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.proyecto_id_seq", allocationSize=1, initialValue=1)
     */

    private $id;
    
   
    /**
     * @var \Nivelorganizacional
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank(message="Debe seleccionar una unidad.")
     */
    private $nivelorganizacional;
    /**
     * @var string
     *
     * @ORM\Column(name="fechacreacion", type="datetime", nullable=false)
     */
    private $fechacreacion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nivelorganizacional = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Proyecto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    public function setInformegestion($informegestion)
    {
        $this->informegestion = $informegestion;
    
        return $this;
    }

    /**
     * Get informegestion
     *
     * @return string 
     */
    public function getInformegestion()
    {
        return $this->informegestion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Proyecto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechainicioestimada
     *
     * @param \DateTime $fechainicioestimada
     * @return Proyecto
     */
    public function setFechainicioestimada($fechainicioestimada)
    {
        $this->fechainicioestimada = $fechainicioestimada;
    
        return $this;
    }

    /**
     * Get fechainicioestimada
     *
     * @return \DateTime 
     */
    public function getFechainicioestimada()
    {
        return $this->fechainicioestimada;
    }

    /**
     * Set fechainicioreal
     *
     * @param \DateTime $fechainicioreal
     * @return Proyecto
     */
    public function setFechainicioreal($fechainicioreal)
    {
        $this->fechainicioreal = $fechainicioreal;
    
        return $this;
    }

    /**
     * Get fechainicioreal
     *
     * @return \DateTime 
     */
    public function getFechainicioreal()
    {
        return $this->fechainicioreal;
    }

    /**
     * Set fechafinestimada
     *
     * @param \DateTime $fechafinestimada
     * @return Proyecto
     */
    public function setFechafinestimada($fechafinestimada)
    {
        $this->fechafinestimada = $fechafinestimada;
    
        return $this;
    }

    /**
     * Get fechafinestimada
     *
     * @return \DateTime 
     */
    public function getFechafinestimada()
    {
        return $this->fechafinestimada;
    }

    /**
     * Set fechafinreal
     *
     * @param \DateTime $fechafinreal
     * @return Proyecto
     */
    public function setFechafinreal($fechafinreal)
    {
        $this->fechafinreal = $fechafinreal;
    
        return $this;
    }

    /**
     * Get fechafinreal
     *
     * @return \DateTime 
     */
    public function getFechafinreal()
    {
        return $this->fechafinreal;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Proyecto
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return integer 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set porcentaje
     *
     * @param integer $porcentaje
     * @return Proyecto
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;
    
        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return integer 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
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
     * Set nivelorganizacional
     *
     * @param \Progis\TicketBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return Ticket
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional = null)
    {
        $this->nivelorganizacional = $nivelorganizacional;
    
        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Progis\TicketBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }
    
    public function __toString() {
        return $this->getNombre();
    }
    /**
     * Set fechacreacion
     *
     * @param \DateTime $fechacreacion
     * @return ProcesarActividad
     */
    public function setFechacreacion($fechacreacion)
    {
        $this->fechacreacion = $fechacreacion;
    
        return $this;
    }

    /**
     * Get fechacreacion
     *
     * @return \DateTime 
     */
    public function getFechacreacion()
    {
        return $this->fechacreacion;
    }
}