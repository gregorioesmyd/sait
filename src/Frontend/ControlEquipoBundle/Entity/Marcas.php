<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Johann Zerpa <jozerpa@telesurtv.net>
 */

/**
 * Marcas
 *
 * @ORM\Table(name="controlequipo.marcas")
 * @ORM\Entity
 */
class Marcas
{
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.marcas_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_marca", type="string", length=255, nullable=false)
     */
    private $descripcionMarca;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\ControlEquipoBundle\Entity\Modelos", mappedBy="modelos")
     */
    protected $modelos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set descripcionMarca
     *
     * @param string $descripcionMarca
     * @return Marcas
     */
    public function setDescripcionMarca($descripcionMarca)
    {
        $this->descripcionMarca = $descripcionMarca;
        return $this;
    }

    /**
     * Get descripcionMarca
     *
     * @return string 
     */
    public function getDescripcionMarca()
    {
        return $this->descripcionMarca;
    }
    
    /**
     * Add provinces
     *
     * @param  \SMTC\MainBundle\Entity\Province $modelos
     * @return Country
     */
    public function addModelo(\Frontend\ControlEquipoBundle\Entity\Modelos $modelos)
    {
        $this->modelos[] = $modelos;

        return $this;
    }

    /**
     * Remove modelos
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Modelos $modelos
     */
    public function removeModelo(\Frontend\ControlEquipoBundle\Entity\Modelos $modelos)
    {
        $this->modelos->removeElement($modelos);
    }

    /**
     * Get modelos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelos()
    {
        return $this->modelos;
    }
    
    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->descripcionMarca;
    }
}