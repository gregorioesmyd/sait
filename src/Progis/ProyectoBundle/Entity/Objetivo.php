<?php

namespace Progis\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Objetivo
 *
 * @ORM\Table(name="progis.objetivo")
 * @ORM\Entity
 */
class Objetivo
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.objetivo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var integer
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */

    private $estatus;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank(message="El nombre no puede estar en blanco.")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     * @Assert\NotBlank(message="La descripción no puede estar en blanco.")
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicioestimada", type="date", nullable=false)
     * @Assert\NotBlank(message="La fecha de inicio estimada no puede estar en blanco.")
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
     * @ORM\Column(name="fechafinestimada", type="date", nullable=false)
     * @Assert\NotBlank(message="La fecha de fin estimada no puede estar en blanco.")
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
     *
     * @ORM\Column(name="porcentaje", type="integer", nullable=false)
     */
    private $porcentaje;


    /**
     * @var \Progis\ProyectoBundle\Entity\Proyecto
     *
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\Proyecto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")
     * })
     */
    private $proyecto;

    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Etapa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etapa_id", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank(message="Debe seleccionar una etapa.")
     */
    private $etapa;

    /**
     * @var string
     *
     * @ORM\Column(name="fechacreacion", type="datetime", nullable=false)
     */
    private $fechacreacion;

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
     * Set estatus
     *
     * @param integer $estatus
     * @return Objetivo
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
     * Set nombre
     *
     * @param string $nombre
     * @return Objetivo
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

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Objetivo
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
     * @return Objetivo
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
     * @return Objetivo
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
     * @return Objetivo
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
     * @return Objetivo
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
     * Set porcentaje
     *
     * @param integer $porcentaje
     * @return Objetivo
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
     * Set proyecto
     *
     * @param \Progis\ProyectoBundle\Entity\Proyecto $proyecto
     * @return Objetivo
     */
    public function setProyecto(\Progis\ProyectoBundle\Entity\Proyecto $proyecto = null)
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
     * Set etapa
     *
     * @param \Progis\ProyectoBundle\Entity\Etapa $etapa
     * @return Objetivo
     */
    public function setEtapa(\Progis\ProyectoBundle\Entity\Etapa $etapa = null)
    {
        $this->etapa = $etapa;
    
        return $this;
    }

    /**
     * Get etapa
     *
     * @return \Progis\ProyectoBundle\Entity\Etapa 
     */
    public function getEtapa()
    {
        return $this->etapa;
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