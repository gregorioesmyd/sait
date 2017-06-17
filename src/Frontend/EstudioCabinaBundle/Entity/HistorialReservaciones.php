<?php

namespace Frontend\EstudioCabinaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estudiocabina.historialReservaciones
 *
 * @ORM\Table(name="estudiocabina.historial_reservaciones")
 * @ORM\Entity
 */
class HistorialReservaciones
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="estudiocabina.historial_reservaciones_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

     /**
     * @var integer
     *
     * @ORM\Column(name="perfil_id", type="integer", nullable=false)
     */
    private $perfil;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora", type="datetime", nullable=false)
     */
    private $fechaHora;

    /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus;
   

     /**
     * @var \Estudiocabina.reservaciones
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EstudioCabinaBundle\Entity\Reservaciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reservaciones_id", referencedColumnName="id")
     * })
     * 
     */
    
    private $reservaciones;

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
     * Set perfil
     *
     * @param integer $perfil
     * @return Reservaciones
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    
        return $this;
    }

    /**
     * Get perfil
     *
     * @return integer 
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set fechaHora
     *
     * @param \DateTime $fechaHora
     * @return HistorialReservaciones
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;
    
        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return \DateTime 
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return HistorialReservaciones
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
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
     * Set reservaciones
     *
     * @param \Frontend\EstudioCabinaBundle\Entity\reservaciones $reservaciones
     * @return HistorialReservaciones
     */
    public function setReservaciones(\Frontend\EstudioCabinaBundle\Entity\Reservaciones $reservaciones = null)
    {
        $this->reservaciones = $reservaciones;
    
        return $this;
    }

    /**
     * Get reservaciones
     *
     * @return \Frontend\EstudioCabinaBundle\Entity\reservaciones 
     */
    public function getReservaciones()
    {
        return $this->reservaciones;
    }
}