<?php

namespace Frontend\EstudioCabinaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estudiocabina.protocolo
 *
 * @ORM\Table(name="estudiocabina.protocolo")
 * @ORM\Entity
 */
class Protocolo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="estudiocabina.protocolo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Estudiocabina.reservaciones
     *
     * @ORM\OneToOne(targetEntity="Frontend\EstudioCabinaBundle\Entity\Reservaciones", inversedBy="protocolo")
     * @ORM\JoinColumn(name="reservaciones_id", referencedColumnName="id")
     *  
     */
    private $reservacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_personas", type="integer", nullable=false)
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\NotBlank(message="Debe indicar el número de personas.")
     */
    private $nroPersonas;
    /**
     * @var string
     *
     * @ORM\Column(name="refrigerio",type="string", length=255 )
     * @Assert\NotBlank(message="Debe indicar el tipo de refrigerio.")
     *
     */
    private $refrigerio;

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
     * Set refrigerio
     *
     * @param string $refrigerio
     * @return Protocolo
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
     * Set reservacion
     *
     * @param \Frontend\EstudioCabinaBundle\Entity\reservaciones $reservacion
     * @return Protocolo
     */
    public function setReservacion(\Frontend\EstudioCabinaBundle\Entity\Reservaciones $reservacion = null)
    {
        $this->reservacion = $reservacion;
    
        return $this;
    }

    /**
     * Get reservacion
     *
     * @return \Frontend\EstudioCabinaBundle\Entity\reservaciones 
     */
    public function getReservacion()
    {
        return $this->reservacion;
    }


    /**
     * Set nroPersonas
     *
     * @param integer $nroPersonas
     * @return Protocolo
     */
    public function setNroPersonas($nroPersonas)
    {
        $this->nroPersonas = $nroPersonas;
    
        return $this;
    }

    /**
     * Get nroPersonas
     *
     * @return integer 
     */
    public function getNroPersonas()
    {
        return $this->nroPersonas;
    }
}