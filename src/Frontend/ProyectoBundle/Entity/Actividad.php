<?php

namespace Frontend\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Actividad
 *
 * @ORM\Table(name="proyecto.actividad", indexes={@ORM\Index(name="IDX_8DF2BD066D5BDFE1", columns={"tarea_id"})})
 * @ORM\Entity
 */
class Actividad
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="proyecto.actividad_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tiempoestimado", type="integer", nullable=false)
     * @Assert\NotBlank(message="El tiempo estimado no deben estar en blanco.")
     * @Assert\Type(type="digit", message="El tiempo debe ser numérico").
     * @Assert\NotEqualTo(value = "0", message="El tiempo no puede ser igual a 0.")
     */
    private $tiempoestimado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comienzo", type="datetime", nullable=true)
     */
    private $comienzo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fin", type="datetime", nullable=true)
     */
    private $fin;
    
    /**
     * @var string
     *
     * @ORM\Column(name="retardo", type="boolean", nullable=true)
     */
    private $retardo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="correoretardoproceso", type="boolean", nullable=false)
     */
    private $correoretardoproceso;

   
    /**
     * @var string
     *
     * @ORM\Column(name="tiemporeal", type="string", nullable=true)
     */
    private $tiemporeal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipotiempo", type="integer", nullable=true)
     */
    //1 dias, 2 horas y 3 minutos
    private $tipotiempo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="integer", nullable=false)
     */
    private $ubicacion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     * @Assert\NotBlank(message="La descripción no debe estar en blanco.")
     */
    private $descripcion;
    


    /**
     * @var \Frontend\ProyectoBundle\Entity\Tarea
     *
     * @ORM\ManyToOne(targetEntity="Frontend\ProyectoBundle\Entity\Tarea")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarea_id", referencedColumnName="id")
     * })
     */
    private $tarea;

        /**
     * @var \Frontend\ProyectoBundle\Entity\Tarea
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un responsable.")
     */
    private $responsable;
    
    /**
     * Set ubicacion
     *
     * @param integer $ubicacion
     * @return Actividad
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return integer 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }
    
    /**
     * Set comienzo
     *
     * @param integer $comienzo
     * @return Actividad
     */
    public function setComienzo($comienzo)
    {
        $this->comienzo = $comienzo;

        return $this;
    }

    /**
     * Get comienzo
     *
     * @return integer 
     */
    public function getComienzo()
    {
        return $this->comienzo;
    }
    
    /**
     * Set retardo
     *
     * @param integer $retardo
     * @return Actividad
     */
    public function setRetardo($retardo)
    {
        $this->retardo = $retardo;

        return $this;
    }

    /**
     * Get retardo
     *
     * @return integer 
     */
    public function getRetardo()
    {
        return $this->retardo;
    }
    
    /**
     * Set correoretardoproceso
     *
     * @param integer $correoretardoproceso
     * @return Actividad
     */
    public function setCorreoretardoproceso($correoretardoproceso)
    {
        $this->correoretardoproceso = $correoretardoproceso;

        return $this;
    }

    /**
     * Get correoretardoproceso
     *
     * @return integer 
     */
    public function getCorreoretardoproceso()
    {
        return $this->correoretardoproceso;
    }
    
    /**
     * Set fin
     *
     * @param integer $fin
     * @return Actividad
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return integer 
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set tiempoestimado
     *
     * @param integer $tiempoestimado
     * @return Actividad
     */
    public function setTiempoestimado($tiempoestimado)
    {
        $this->tiempoestimado = $tiempoestimado;

        return $this;
    }

    /**
     * Get tiempoestimado
     *
     * @return integer 
     */
    public function getTiempoestimado()
    {
        return $this->tiempoestimado;
    }
    
    /**
     * Set tiemporeal
     *
     * @param integer $tiemporeal
     * @return Actividad
     */
    public function setTiemporeal($tiemporeal)
    {
        $this->tiemporeal = $tiemporeal;

        return $this;
    }

    /**
     * Get tiemporeal
     *
     * @return integer 
     */
    public function getTiemporeal()
    {
        return $this->tiemporeal;
    }
    
    /**
     * Set tipotiempo
     *
     * @param integer $tipotiempo
     * @return Actividad
     */
    public function setTipotiempo($tipotiempo)
    {
        $this->tipotiempo = $tipotiempo;

        return $this;
    }

    /**
     * Get tipotiempo
     *
     * @return integer 
     */
    public function getTipotiempo()
    {
        return $this->tipotiempo;
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Actividad
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tarea
     *
     * @param \Frontend\ProyectoBundle\Entity\Tarea $tarea
     * @return Actividad
     */
    public function setTarea(\Frontend\ProyectoBundle\Entity\Tarea $tarea = null)
    {
        $this->tarea = $tarea;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return \Frontend\ProyectoBundle\Entity\Responsable 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
    
    /**
     * Set responsable
     *
     * @param \Frontend\ProyectoBundle\Entity\Responsable $responsable
     * @return Actividad
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get tarea
     *
     * @return \Frontend\ProyectoBundle\Entity\Tarea 
     */
    public function getTarea()
    {
        return $this->tarea;
    }
    
    public function __toString() {
        return $this->getDescripcion();
    }
}
