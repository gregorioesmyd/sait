<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitudes.estatus
 *
 * @ORM\Table(name="solicitudes.estatus")
 * @ORM\Entity
 */
class Estatus
{
    /** 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.estatus_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="estatus", type="string", length=20, nullable=false)
     */
    private $estatus;

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
     * Set estatus
     *
     * @param string $estatus
     * @return Estatus
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return string 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    public function __toString()
    {
        return $this->getEstatus();
    }
    
}