<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetallePrestamo
 *
 * @ORM\Table(name="videoteca.detallePrestamo")
 * @ORM\Entity
 */
class DetallePrestamo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *@ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta")
     *@ORM\JoinColumn(name="id_cinta", referencedColumnName="id")
     */
    private $idCinta;
    
    /**
     * @var \Prestamo
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Prestamo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_prestamo", referencedColumnName="id", nullable=false)
     * })
     */
    private $idPrestamo;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_prestamo", type="datetime")
     */
    private $fechaPrestamo;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_devolucion", type="datetime", nullable=true)
     */
    private $fechaDevolucion;

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
     * Set idCinta
     *
     * @param \Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta $idCinta
     * @return DetallePrestamo
     */
    
    public function setIdCinta(\Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta $idCinta = null)
    {
        $this->idCinta = $idCinta;
    
        return $this;
    }

    /**
     * Get idCinta
     *
     *@return \Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta  
     */
    public function getIdCinta()
    {
        return $this->idCinta;
    }
    
    /**
     * Set idPrestamo
     *
     * @param \Frontend\VideotecaBundle\Entity\Prestamo $idPrestamo
     * @return DetallePrestamo
     */
    public function setIdPrestamo(\Frontend\VideotecaBundle\Entity\Prestamo $idPrestamo)
    {
        $this->idPrestamo = $idPrestamo;
    
        return $this;
    }

    /**
     * Get idPrestamo
     *
     * @return \Frontend\VideotecaBundle\Entity\Prestamo 
     */
    public function getIdPrestamo()
    {
        return $this->idPrestamo;
    }
    
      /**
     * Set fechaPrestamo
     *
     * @param \DateTime $fechaPrestamo
     * @return DetallePrestamo
     */
    public function setFechaPrestamo($fechaPrestamo)
    {
        $this->fechaPrestamo = $fechaPrestamo;
    
        return $this;
    }

    /**
     * Get fechaPrestamo
     *
     * @return \DateTime 
     */
    public function getFechaPrestamo()
    {
        return $this->fechaPrestamo;
    }
    
     /**
     * Set fechaDevolucion
     *
     * @param \DateTime $fechaDevolucion
     * @return DetallePrestamo
     */
    public function setFechaDevolucion($fechaDevolucion)
    {
        $this->fechaDevolucion = $fechaDevolucion;
    
        return $this;
    }

    /**
     * Get fechaDevolucion
     *
     * @return \DateTime 
     */
    public function getFechaDevolucion()
    {
        return $this->fechaDevolucion;
    }
}

