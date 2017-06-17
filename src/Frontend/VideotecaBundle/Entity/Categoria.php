<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="videoteca.categoria")
 * @ORM\Entity
 */
class Categoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @ORM\Column(name="correlativo", type="string", length=255)
     */
     private $correlativo;

    /**
     * @ORM\ManyToOne(targetEntity="TipoCinta", inversedBy="categorias")
     * @ORM\JoinColumn(name="tipocinta_id", referencedColumnName="id")
     */
    private $tipoCinta;

    /**
     * [__toString description]
     * @return string [description]
     */
    public function __toString()
    {
        return $this->getNombre();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Categoria
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set correlativo
     *
     * @param string $correlativo
     * @return Categoria
     */
    public function setCorrelativo($correlativo)
    {
        $this->correlativo = $correlativo;

        return $this;
    }

    /**
     * Get correlativo
     *
     * @return string
     */
    public function getCorrelativo()
    {
        return $this->correlativo;
    }

    /**
     * Set tipoCinta
     *
     * @param \Frontend\VideotecaBundle\Entity\TipoCinta $tipoCinta
     * @return Categoria
     */
    public function setTipoCinta(\Frontend\VideotecaBundle\Entity\TipoCinta $tipoCinta = null)
    {
        $this->tipoCinta = $tipoCinta;

        return $this;
    }

    /**
     * Get tipoCinta
     *
     * @return \Frontend\VideotecaBundle\Entity\TipoCinta
     */
    public function getTipoCinta()
    {
        return $this->tipoCinta;
    }
}