<?php

namespace Frontend\TruequesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Frontend\TruequesBundle\Entity\Producto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categoria
 *
 * @ORM\Table(name="trueques.categoria")
 * @ORM\Entity
 */
class Categoria
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
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank(message="Debe seleccionar el nombre de la categoria.")
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\TruequesBundle\Entity\Producto", mappedBy="categoria", cascade={"persist", "remove"})
     **/
    private $productos;

    public function __construct() {
        $this->productos = new ArrayCollection();
    }

    /**
     *  
     */
    public function __toString()
    {
        return $this->nombre;
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
     * Add productos
     *
     * @param \Frontend\TruequesBundle\Entity\Producto $productos
     * @return Categoria
     */
    public function addProducto(\Frontend\TruequesBundle\Entity\Producto $productos)
    {
        $this->productos[] = $productos;
    
        return $this;
    }

    /**
     * Remove productos
     *
     * @param \Frontend\TruequesBundle\Entity\Producto $productos
     */
    public function removeProducto(\Frontend\TruequesBundle\Entity\Producto $productos)
    {
        $this->productos->removeElement($productos);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductos()
    {
        return $this->productos;
    }
}