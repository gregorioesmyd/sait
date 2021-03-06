<?php

namespace Progis\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Procesar
 *
 * @ORM\Table(name="progis.procesaractividad")
 * @ORM\Entity
 */
class ProcesarActividad
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.procesaractividad_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     
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
     * @ORM\Column(name="correoretardo", type="boolean", nullable=false)
     */
    private $correoretardo;

   
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
     * @ORM\Column(name="ubicacionPriometa", type="integer", nullable=false)
     */
    private $ubicacionPriometa;
    
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Miembroproyecto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un usuario.")
     */
    private $responsable;
    
    /**
     * @var string
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    //1 dias, 2 horas y 3 minutos
    private $orden;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="ordenPriometa", type="integer", nullable=true)
     */
    //1 dias, 2 horas y 3 minutos
    private $ordenPriometa;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     * @Assert\NotBlank(message="La descripción no debe estar en blanco.")
     */
    private $descripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechacreacion", type="datetime", nullable=false)
     */
    private $fechacreacion;
    


    /**
     * @var \Progis\ProyectoBundle\Entity\Objetivo
     *
     * @ORM\ManyToOne(targetEntity="Progis\ProyectoBundle\Entity\Objetivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objetivo_id", referencedColumnName="id")
     * })
     */
    private $objetivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="justificacion", type="boolean", nullable=true)
     */
    private $justificacion;
    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registradopor_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $registradopor;

    /**
     * @var string
     *
     * @ORM\Column(name="valida", type="boolean", nullable=true)
     */
    private $valida;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setJustificacion($justificacion)
    {
        $this->justificacion = $justificacion;
    
        return $this;
    }

    public function getJustificacion()
    {
        return $this->justificacion;
    }
    
    /**
     * Set orden
     *
     * @param integer $orden
     * @return Procesar
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }
    
    
    /**
     * Set ubicacion
     *
     * @param integer $ubicacion
     * @return Procesar
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
     * @return Procesar
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
     * @return Procesar
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
     * Set correoretardo
     *
     * @param integer $correoretardo
     * @return Procesar
     */
    public function setCorreoretardo($correoretardo)
    {
        $this->correoretardo = $correoretardo;

        return $this;
    }

    /**
     * Get correoretardo
     *
     * @return integer 
     */
    public function getCorreoretardo()
    {
        return $this->correoretardo;
    }
    
    /**
     * Set fin
     *
     * @param integer $fin
     * @return Procesar
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
     * @return Procesar
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
     * @return Procesar
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
     * @return Procesar
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
     * @return Procesar
     */
    public function setResponsable(\Progis\ProyectoBundle\Entity\Miembroproyecto $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
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
     * Set objetivo
     *
     * @param \Progis\ProyectoBundle\Entity\Objetivo $objetivo
     * @return Actividad
     */
    public function setObjetivo(\Progis\ProyectoBundle\Entity\Objetivo $objetivo = null)
    {
        $this->objetivo = $objetivo;

        return $this;
    }
    
    /**
     * Get objetivo
     *
     * @return \Progis\ProyectoBundle\Entity\Objetivo 
     */
    public function getObjetivo()
    {
        return $this->objetivo;
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

    /**
     * Set ordenPriometa
     *
     * @param integer $ordenPriometa
     * @return ProcesarActividad
     */
    public function setOrdenPriometa($ordenPriometa)
    {
        $this->ordenPriometa = $ordenPriometa;
    
        return $this;
    }

    /**
     * Get ordenPriometa
     *
     * @return integer 
     */
    public function getOrdenPriometa()
    {
        return $this->ordenPriometa;
    }

    /**
     * Set ubicacionPriometa
     *
     * @param integer $ubicacionPriometa
     * @return ProcesarActividad
     */
    public function setUbicacionPriometa($ubicacionPriometa)
    {
        $this->ubicacionPriometa = $ubicacionPriometa;
    
        return $this;
    }

    /**
     * Get ubicacionPriometa
     *
     * @return integer 
     */
    public function getUbicacionPriometa()
    {
        return $this->ubicacionPriometa;
    }
    
    /**
     * Set registradopor
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $registradopor
     * @return Operador
     */
    public function setRegistradopor(\Administracion\UsuarioBundle\Entity\Perfil $registradopor = null)
    {
        $this->registradopor = $registradopor;
    
        return $this;
    }

    /**
     * Get registradopor
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getRegistradopor()
    {
        return $this->registradopor;
    }

    /**
     * Set valida
     *
     * @param boolean $valida
     * @return ProcesarActividad
     */
    public function setValida($valida)
    {
        $this->valida = $valida;
    
        return $this;
    }

    /**
     * Get valida
     *
     * @return boolean 
     */
    public function getValida()
    {
        return $this->valida;
    }
}