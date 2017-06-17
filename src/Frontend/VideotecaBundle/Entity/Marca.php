<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marca
 *
 * @ORM\Table(name="videoteca.marca")
 * @ORM\Entity
 */
class Marca
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
     * @var string
     *
     * @ORM\Column(name="descripcion_marca", type="string", length=255)
     */
    private $descripcionMarca;

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
     * @return Marca
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

    public function __toString()
    {
        return $this->getDescripcionMarca();
    }
}