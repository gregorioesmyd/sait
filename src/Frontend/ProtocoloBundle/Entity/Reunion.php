<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * protocolo.Reunion
 *
 * @ORM\Table(name="protocolo.reunion")
 * @ORM\Entity
 */
class Reunion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.reunion_id_seq", allocationSize=1, initialValue=1)
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
     * @Assert\NotBlank(message="Debe indicar el lugar de la reunión.")
     */
    private $lugar;

    /**
     * @var integer
     *
     *@ORM\Column(name="nro_personas", type="integer", nullable=false)
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\NotBlank(message="Debe indicar el lugar de la reunión.")
     */
    private $numeroPersonas;

    /**
     * @var string
     *
     * @ORM\Column(name="refrigerio",type="string", length=255 )
     * @Assert\NotBlank(message="Debe indicar el refrigerio.")
     */
    private $refrigerio;

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
     * @return Reunion
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
     * @return Reunion
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
     * @return Reunion
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
     * Set numeroPersonas
     *
     * @param integer $numeroPersonas
     * @return Reunion
     */
    public function setNumeroPersonas($numeroPersonas)
    {
        $this->numeroPersonas = $numeroPersonas;
    
        return $this;
    }

    /**
     * Get numeroPersonas
     *
     * @return integer 
     */
    public function getNumeroPersonas()
    {
        return $this->numeroPersonas;
    }

    /**
     * Set refrigerio
     *
     * @param string $refrigerio
     * @return Reunion
     */
    public function setRefrigerio($refrigerio)
    {
        $this->refrigerio = $refrigerio;
    
        return $this;
    }

    /**
     * Get refrigerio
     *
     * @return string
     */
    public function getRefrigerio()
    {
        return $this->refrigerio;
    }

    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Reunion
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
