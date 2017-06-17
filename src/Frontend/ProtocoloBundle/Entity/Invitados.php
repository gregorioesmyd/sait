<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * protocolo.Invitados
 *
 * @ORM\Table(name="protocolo.invitados")
 * @ORM\Entity
 */
class Invitados
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.invitados_id_seq", allocationSize=1, initialValue=1)
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
     * @Assert\NotBlank(message="Debe indicar la hora de llegada del invitado.")
     */
    private $hora;


    /**
     * @var string
     *
     *@ORM\Column(name="invitados", type="string", nullable=false)
     *@Assert\NotBlank(message="Debe indicar el nombre del invitado.")
     */
    private $invitados;

    /**
     * @var boolean
     * @ORM\Column(name="transporte_personal", type="boolean", nullable=true)
     *
     *
     * @Assert\NotBlank(message="Indique si tiene transporte personal."))
     */
    private $transportePersonal;

    /**
     * @var \Time
     *
     *@ORM\Column(name="hora_estudio", type="time", nullable=false)
     *@Assert\NotBlank(message="Debe indicar la hora que el invitado entrará al estudio..")
     */
    private $horaEstudio;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_entrevista", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank(message="Debe de seleccionar el responsable de la entrevista del invitado."))
     */
    private $responsableEntrevista;

    /**
     * @var boolean
     * @ORM\Column(name="wifi", type="boolean", nullable=true)
     *
     * @Assert\NotBlank(message="Debe indicar si usara el servicio de WIFI."))
     */
    private $wifi;

    /**
     * @var string
     *
     *@ORM\Column(name="motivo", type="text", nullable=true)
     *
     * @Assert\NotBlank(message="Debe indicar el motivo o programa al cual asistirá el invitado."))
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "La sinopsis debe ser al menos {{ limit }} caracteres"
     * )
     *
     */
    private $motivo;

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
     * @return Invitados
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
     * @return Invitados
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
     * Set invitados
     *
     * @param string $invitados
     * @return Invitados
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
     * Set transportePersonal
     *
     * @param boolean $transportePersonal
     * @return Invitados
     */
    public function setTransportePersonal($transportePersonal)
    {
        $this->transportePersonal = $transportePersonal;
    
        return $this;
    }

    /**
     * Get transportePersonal
     *
     * @return boolean 
     */
    public function getTransportePersonal()
    {
        return $this->transportePersonal;
    }

    /**
     * Set horaEstudio
     *
     * @param \Time $horaEstudio
     * @return Invitados
     */
    public function setHoraEstudio($horaEstudio)
    {
        $this->horaEstudio = $horaEstudio;
    
        return $this;
    }

    /**
     * Get horaEstudio
     *
     * @return \Time 
     */
    public function getHoraEstudio()
    {
        return $this->horaEstudio;
    }

    /**
     * Set responsableEntrevista
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsableEntrevista
     * @return Invitados
     */
    public function setResponsableEntrevista(\Administracion\UsuarioBundle\Entity\Perfil $responsableEntrevista)
    {
        $this->responsableEntrevista = $responsableEntrevista;
    
        return $this;
    }

    /**
     * Get responsableEntrevista
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil  
     */
    public function getResponsableEntrevista()
    {
        return $this->responsableEntrevista;
    }

    /**
     * Set wifi
     *
     * @param boolean $wifi
     * @return Invitados
     */
    public function setWifi($wifi)
    {
        $this->wifi = $wifi;
    
        return $this;
    }

    /**
     * Get wifi
     *
     * @return boolean 
     */
    public function getWifi()
    {
        return $this->wifi;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return Invitados
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    
        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Invitados
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
