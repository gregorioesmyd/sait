<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * protocolo.Regalo
 *
 * @ORM\Table(name="protocolo.regalo")
 * @ORM\Entity
 */
class Regalo
{
     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.regalo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime")
     * @Assert\NotBlank(message="Debe seleccionar la fecha de entrega de los regalos.")
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_regalos", type="integer")
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\NotBlank(message="Debe indicar el número de regalos.")
     */
    private $nroRegalos;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_regalo",type="string", length=255 )
     * @Assert\NotBlank(message="Debe indicar el tipo de regalo.")
     */
    private $tipoRegalo;

    /**
     * @var string
     *
     *@ORM\Column(name="dirigido", type="string", nullable=false)
     * @Assert\NotBlank(message="Debe indicar a quien va dirigido los regalos.")
     */
    private $dirigido;

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
     * @param \DateTime $fecha
     * @return Regalo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set nroRegalos
     *
     * @param integer $nroRegalos
     * @return Regalo
     */
    public function setNroRegalos($nroRegalos)
    {
        $this->nroRegalos = $nroRegalos;
    
        return $this;
    }

    /**
     * Get nroRegalos
     *
     * @return integer 
     */
    public function getNroRegalos()
    {
        return $this->nroRegalos;
    }

    /**
     * Set tipoRegalo
     *
     * @param string $tipoRegalo
     * @return Regalo
     */
    public function setTipoRegalo($tipoRegalo)
    {
        $this->tipoRegalo = $tipoRegalo;
    
        return $this;
    }

    /**
     * Get tipoRegalo
     *
     * @return string
     */
    public function getTipoRegalo()
    {
        return $this->tipoRegalo;
    }

    /**
     * Set dirigido
     *
     * @param string $dirigido
     * @return Regalo
     */
    public function setDirigido($dirigido)
    {
        $this->dirigido = $dirigido;
    
        return $this;
    }

    /**
     * Get dirigido
     *
     * @return string 
     */
    public function getDirigido()
    {
        return $this->dirigido;
    }

    
    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Regalo
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
