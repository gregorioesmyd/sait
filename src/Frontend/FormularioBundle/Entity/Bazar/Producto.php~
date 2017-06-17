<?php

namespace Frontend\FormularioBundle\Entity\Bazar;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 * 
 * @ORM\Table(name="formulario.producto_bazar")
 * @ORM\Entity
 */
class Producto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="formulario.producto_bazar_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     *
     * @ORM\Column(name="descripcion", type="string", length=100, nullable=false)
     */
    private $descripcion;
    
    /**
     *
     * @ORM\Column(name="comida", type="boolean", nullable=true)
     */
    private $comida;


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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Producto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    public function __toString(){
        return $this->getDescripcion();
    }

    /**
     * Set comida
     *
     * @param boolean $comida
     * @return Producto
     */
    public function setComida($comida)
    {
        $this->comida = $comida;
    
        return $this;
    }

    /**
     * Get comida
     *
     * @return boolean 
     */
    public function getComida()
    {
        return $this->comida;
    }
}