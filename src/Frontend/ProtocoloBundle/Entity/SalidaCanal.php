<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * protocolo.SalidaCanal
 *
 * @ORM\Table(name="protocolo.salidaCanal")
 * @ORM\Entity
 */
class SalidaCanal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.salida_canal_id_seq", allocationSize=1, initialValue=1)
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
     *@ORM\Column(name="lugar", type="string", nullable=false)
     * @Assert\Length(
     *      min = 11,
     *      max = 500,
     *      minMessage = "La dirección debe tener un minimo de caracteres de {{ limit }} caracteres",
     *      maxMessage = "La dirección tiene un maximo de  {{ limit }} caracteres"
     * )
     * @Assert\NotBlank(message="Debe indicar el lugar.")
     */
    private $lugar;

    /**
     * @var string
     *
     *@ORM\Column(name="invitados", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el nombre del invitado.")
     */
    private $invitados;

    /**
     * @var integer
     *
     *@ORM\Column(name="tfl_invitado", type="string", nullable=false)
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\Length(
     *      min = 11,
     *      max = 11,
     *      minMessage = "You must be at least {{ limit }}cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }}cm to enter"
     * )
     * @Assert\NotBlank(message="Debe indicar el número de Telefono del invitado.")
     */
    private $tlfInvitado;

    /**
     * @var string
     *
     *@ORM\Column(name="observacion", type="string", nullable=false)
     * @Assert\NotBlank(message="Debe indicar una observación.")
     */
    private $observacion;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante", referencedColumnName="id")
     * })
     *  
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
     * @return SalidaCanal
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
     * @return SalidaCanal
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
     * Set lugar
     *
     * @param string $lugar
     * @return SalidaCanal
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;
    
        return $this;
    }

    /**
     * Get lugar
     *
     * @return string 
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * Set invitados
     *
     * @param string $invitados
     * @return SalidaCanal
     */
    public function setInvitados($invitados)
    {
        $this->invitados = $invitados;
    
        return $this;
    }

    /**
     * Get invitados
     *
     * @return string 
     */
    public function getInvitados()
    {
        return $this->invitados;
    }

    /**
     * Set tlfInvitado
     *
     * @param integer $tlfInvitado
     * @return SalidaCanal
     */
    public function setTlfInvitado($tlfInvitado)
    {
        $this->tlfInvitado = $tlfInvitado;
    
        return $this;
    }

    /**
     * Get tlfInvitado
     *
     * @return integer 
     */
    public function getTlfInvitado()
    {
        return $this->tlfInvitado;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return SalidaCanal
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return SalidaCanal
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
