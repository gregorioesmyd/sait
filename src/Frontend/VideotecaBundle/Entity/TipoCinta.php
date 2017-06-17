<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TipoCinta
 *
 * @ORM\Table(name="videoteca.tipocinta")
 * @ORM\Entity()
 */
class TipoCinta
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
     * @ORM\Column(name="nombre", type="string", length=10)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string")
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Categoria", mappedBy="tipoCinta")
     */
    private $categorias;

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
     * @return TipoCinta
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
     * [__toString description]
     * @return string [description]
     */
    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return TipoCinta
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categoria = new ArrayCollection();
    }

    /**
     * Add categorias
     *
     * @param \Frontend\VideotecaBundle\Entity\Categoria $categorias
     * @return TipoCinta
     */
    public function addCategoria(\Frontend\VideotecaBundle\Entity\Categoria $categorias)
    {
        $this->categorias[] = $categorias;

        return $this;
    }

    /**
     * Remove categorias
     *
     * @param \Frontend\VideotecaBundle\Entity\Categoria $categorias
     */
    public function removeCategoria(\Frontend\VideotecaBundle\Entity\Categoria $categorias)
    {
        $this->categorias->removeElement($categorias);
    }

    /**
     * Get categorias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategorias()
    {
        return $this->categorias;
    }
}