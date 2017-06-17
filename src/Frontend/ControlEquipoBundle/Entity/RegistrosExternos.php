<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RegistrosExternos
 *
 * @ORM\Table(name="controlequipo.registros_externos")
 * @ORM\Entity
 */
class RegistrosExternos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.registros_externos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;


    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fecha_entrada", type="datetime", nullable=true)
     */
    private $fechaEntrada;
    
    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fecha_salida", type="datetime", nullable=true)
     */
    private $fechaSalida;

    /**
     * @var \EquiposExternos
     *
     * @ORM\ManyToOne(targetEntity="EquiposExternos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Indique un Equipo a Registrar")
     */
    private $equipo;

    /**
     * @var integer
     *
     * @ORM\Column(name="propietario_id", type="integer", nullable=false)
     * @Assert\NotBlank(message="Debe seleccionar el Propietario Interno/Externo")
     */
    private $propietarioId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo", type="integer", nullable=false)
     * @Assert\NotBlank(message="Indique el Tipo de Propietario")
     */
    private $tipo;
    
    /**
     * @var \NivelOrganizacional
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional", inversedBy="equipos_externos")
     * @ORM\JoinTable(name="usuarios.nivelorganizacional",
     *   joinColumns={
     *     @ORM\JoinColumn(name="dependencia_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\NotNull(message="Debe seleccionar La Dependencia")
     */
    private $dependencia;
    
    
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
     * Get $fechaEntrada
     *
     * @return \DateTime 
     */
    function getFechaEntrada() 
    {
        return $this->fechaEntrada;
    }

    /**
     * Get $fechaSalida
     *
     * @return \DateTime 
     */
    function getFechaSalida() 
    {
        return $this->fechaSalida;
    }
    
    /**
     * Get dependencia
     *
     * @return integer 
     */
    function getDependencia() 
    {
        return $this->dependencia;
    }

    /**
     * Get dependencia
     *
     * @return integer 
     */
    function getEquipo() 
    {
        return $this->equipo;
    }
    
    /**
     * Get dependencia
     *
     * @return integer 
     */
    function getTipo() 
    {
        return $this->tipo;
    }
    
    /**
     * Set \DateTime $fechaEntrada
     *
     * @param \DateTime $fechaEntrada
     * @return RegistrosExternos
     */
    function setFechaEntrada(\Datetime $fechaEntrada) 
    {
        $this->fechaEntrada = $fechaEntrada;
        
        return $this;
    }
    
    /**
     * Set \DateTime $fechaSalida
     *
     * @param \DateTime $fechaSalida
     * @return RegistrosExternos
     */
    function setFechaSalida(\Datetime $fechaSalida) 
    {
        $this->fechaSalida = $fechaSalida;
        
        return $this;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return EquiposExternos
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    
    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return EquiposExternos
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
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
     * Set propietarioId
     *
     * @param integer $propietarioId
     * @return EquiposExternos
     */
    public function setPropietarioId($propietarioId)
    {
        $this->propietarioId = $propietarioId;
    
        return $this;
    }

    /**
     * Get propietarioId
     *
     * @return integer 
     */
    public function getPropietarioId()
    {
        return $this->propietarioId;
    }
    
    /**
     * Set dependencia
     *
     * @param \Administracion\UsuarioBundle\Entity\Nivelorganizacional $dependencia
     * @return Dependencia
     */
    public function setDependencia(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $dependencia) 
    {
        $this->dependencia = $dependencia;
    
        return $this;
    }

    /**
     * Set equipo
     *
     * @param \Frontend\ControlEquipoBundle\Entity\EquiposExternos $equipo
     * @return EquiposExternos
     */
    public function setEquipo(\Frontend\ControlEquipoBundle\Entity\EquiposExternos $equipo) 
    {
        $this->equipo = $equipo;
    
        return $this;
    }
}