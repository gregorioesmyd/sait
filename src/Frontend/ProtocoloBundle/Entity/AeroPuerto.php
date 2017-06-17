<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * protocolo.AeroPuerto
 *
 * @ORM\Table(name="protocolo.AeroPuerto")
 * @ORM\Entity
 */
class AeroPuerto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.aeropuerto_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     * @Assert\NotBlank(message="La fecha no puede estar en blanco.")
     */
    private $fecha;

    /**
     * @var \Time
     *
     * @ORM\Column(name="hora", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora no puede estar en blanco.")
     */
    private $hora;

    /**
     * @var string
     *
     *@ORM\Column(name="aeropuerto", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el tipo de aeropuerto.")
     */
    private $aeropuerto;

    /**
     * @var string
     *
     *@ORM\Column(name="nombre_invitado", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el nombre del invitado.")
     */
    private $nombreInvitado;

    /**
     * @var string
     *
     *@ORM\Column(name="linea_aerea", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el nombre de la linea Aerea.")
     */
    private $lineaAerea;

    /**
     * @var integer
     *
     *@ORM\Column(name="nro_vuelo", type="integer", nullable=false)
     *@Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     *@Assert\NotBlank(message="Debe indicar el numero de vuelo")
     */
    private $numeroVuelo;

    /**
     * @var \Pais
     *@ORM\ManyToOne(targetEntity="Frontend\ProtocoloBundle\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pais", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank(message="Debe de seleccionar el país de procedencia."))      
     */
    private $paisProcedencia;

    /**
     * @var string
     *
     *@ORM\Column(name="destino", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el lugar de destino.")
     */
    private $lugarDestino;

    /**
     * @var string
     *
     *@ORM\Column(name="localizador", type="string", nullable=false)
     *@Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     *@Assert\NotBlank(message="Debe indicar el numero de localizador")
     */
    private $localizador;


    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank(message="Debe de seleccionar el responsable del invitado."))
     */
    private $responsable;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante", referencedColumnName="id", nullable=false)
     * })
     * 
     */
    private $solicitante;


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
     * Set fecha
     *
     * @param \Date $fecha
     * @return AereoPuerto
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \Date 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param \Time $hora
     * @return AereoPuerto
     */
    public function setHora($hora)
    {
        $this->hora = $hora;
    
        return $this;
    }

    /**
     * Get hora
     *
     * @return \Time 
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set aeropuerto
     *
     * @param string $aeropuerto
     * @return AereoPuerto
     */
    public function setAeropuerto($aeropuerto)
    {
        $this->aeropuerto = $aeropuerto;
    
        return $this;
    }

    /**
     * Get aeropuerto
     *
     * @return string
     */
    public function getAeropuerto()
    {
        return $this->aeropuerto;
    }

    /**
     * Set nombreInvitado
     *
     * @param string $nombreInvitado
     * @return AereoPuerto
     */
    public function setNombreInvitado($nombreInvitado)
    {
        $this->nombreInvitado = $nombreInvitado;
    
        return $this;
    }

    /**
     * Get nombreInvitado
     *
     * @return string 
     */
    public function getNombreInvitado()
    {
        return $this->nombreInvitado;
    }

    /**
     * Set lineaAerea
     *
     * @param string $lineaAerea
     * @return AereoPuerto
     */
    public function setLineaAerea($lineaAerea)
    {
        $this->lineaAerea = $lineaAerea;
    
        return $this;
    }

    /**
     * Get lineaAerea
     *
     * @return string 
     */
    public function getLineaAerea()
    {
        return $this->lineaAerea;
    }

    /**
     * Set numeroVuelo
     *
     * @param integer $numeroVuelo
     * @return AereoPuerto
     */
    public function setNumeroVuelo($numeroVuelo)
    {
        $this->numeroVuelo = $numeroVuelo;
    
        return $this;
    }

    /**
     * Get numeroVuelo
     *
     * @return integer 
     */
    public function getNumeroVuelo()
    {
        return $this->numeroVuelo;
    }

    /**
     * Set paisProcedencia
     *
     * @param \Frontend\ProtocoloBundle\Entity\Pais $paisProcedencia
     * @return AereoPuerto
     */
    public function setPaisProcedencia(\Frontend\ProtocoloBundle\Entity\Pais $paisProcedencia)
    {
        $this->paisProcedencia = $paisProcedencia;
    
        return $this;
    }

    /**
     * Get paisProcedencia
     *
     * @return \Frontend\ProtocoloBundle\Entity\Pais 
     */
    public function getPaisProcedencia()
    {
        return $this->paisProcedencia;
    }

    /**
     * Set lugarDestino
     *
     * @param string $lugarDestino
     * @return AereoPuerto
     */
    public function setLugarDestino($lugarDestino)
    {
        $this->lugarDestino = $lugarDestino;
    
        return $this;
    }

    /**
     * Get lugarDestino
     *
     * @return string 
     */
    public function getLugarDestino()
    {
        return $this->lugarDestino;
    }

    /**
     * Set localizador
     *
     * @param string $localizador
     * @return AereoPuerto
     */
    public function setLocalizador($localizador)
    {
        $this->localizador = $localizador;
    
        return $this;
    }

    /**
     * Get localizador
     *
     * @return string 
     */
    public function getLocalizador()
    {
        return $this->localizador;
    }

    /**
     * Set responsable
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsable
     * @return AereoPuerto
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable)
    {
        $this->responsable = $responsable;
    
        return $this;
    }

    /**
     * Get responsable
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return AereoPuerto
     */
    public function setSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $solicitante)
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
}
