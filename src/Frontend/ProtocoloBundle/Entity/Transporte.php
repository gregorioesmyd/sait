<?php

namespace Frontend\ProtocoloBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * protocolo.Transporte
 *
 * @ORM\Table(name="protocolo.transporte")
 * @ORM\Entity
 */
class Transporte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="protocolo.transporte_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Time
     *
     * @ORM\Column(name="hora_busqueda", type="time", nullable=false)
     * @Assert\NotBlank(message="Debe indicar la hora de la busqueda.")
     */
    private $horaBusqueda;

    /**
     * @var \Estudiocabina.reservaciones
     *
     * @ORM\OneToOne(targetEntity="Frontend\ProtocoloBundle\Entity\Invitados")
     * @ORM\JoinColumn(name="id_invitado", referencedColumnName="id", nullable=false)
     *  
     */
    private $invitado;

    /**
     * @var integer
     *
     *@ORM\Column(name="tfl_contacto", type="string", nullable=false)
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
     *@Assert\NotBlank(message="Debe indicar el número telefónico de contacto.")
     */
    private $tlfContacto;

    
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
     * Set horaBusqueda
     *
     * @param \Time $horaBusqueda
     * @return Invitados
     */
    public function setHoraBusqueda($horaBusqueda)
    {
        $this->horaBusqueda = $horaBusqueda;
    
        return $this;
    }

    /**
     * Get horaBusqueda
     *
     * @return \Time 
     */
    public function getHoraBusqueda()
    {
        return $this->horaBusqueda;
    }

    /**
     * Set invitado
     *
     * @param \Frontend\ProtocoloBundle\Entity\Invitados $invitado
     * @return Transporte
     */
    public function setInvitado(\Frontend\ProtocoloBundle\Entity\Invitados $invitado)
    {
        $this->invitado = $invitado;
    
        return $this;
    }

    /**
     * Get invitado
     *
     * @return \Frontend\ProtocoloBundle\Entity\Invitados  
     */
    public function getInvitado()
    {
        return $this->invitado;
    }

    /**
     * Set tlfContacto
     *
     * @param integer $tlfContacto
     * @return Transporte
     */
    public function setTlfContacto($tlfContacto)
    {
        $this->tlfContacto = $tlfContacto;
    
        return $this;
    }

    /**
     * Get tlfContacto
     *
     * @return integer 
     */
    public function getTlfContacto()
    {
        return $this->tlfContacto;
    }
}
