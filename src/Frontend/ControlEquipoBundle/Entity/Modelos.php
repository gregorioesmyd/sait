<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Johann Zerpa <jozerpa@telesurtv.net>
 */

/**
 * Modelos
 *
 * @ORM\Table(name="controlequipo.modelos")
 * @ORM\Entity(repositoryClass="Frontend\ControlEquipoBundle\Entity\ModelosRepository")
 */
class Modelos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.modelos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_modelo", type="string", length=255, nullable=false)
     */
    private $descripcionModelo;

    /**
     * @var \Marcas
     *
     * @ORM\ManyToOne(targetEntity="Marcas", inversedBy="modelos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca_id", referencedColumnName="id")
     * })
     */
    private $marca;


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
     * Set descripcionModelo
     *
     * @param string $descripcionModelo
     * @return Modelos
     */
    public function setDescripcionModelo($descripcionModelo)
    {
        $this->descripcionModelo = $descripcionModelo;
    
        return $this;
    }

    /**
     * Get descripcionModelo
     *
     * @return string 
     */
    public function getDescripcionModelo()
    {
        return $this->descripcionModelo;
    }

    /**
     * Set marca
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Marcas $marca
     * @return Modelos
     */
    public function setMarca(\Frontend\ControlEquipoBundle\Entity\Marcas $marca = null)
    {
        $this->marca = $marca;
    
        return $this;
    }

    /**
     * Get marca
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Marcas 
     */
    public function getMarca()
    {
        return $this->marca;
    }
    
    public function __toString()
    {
        return $this->descripcionModelo;
    }
}