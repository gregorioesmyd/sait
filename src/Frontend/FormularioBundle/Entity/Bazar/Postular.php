<?php

namespace Frontend\FormularioBundle\Entity\Bazar;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 * 
 * @ORM\Table(name="formulario.postular_bazar")
 * @ORM\Entity
 */
class Postular
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="formulario.postular_bazar_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    

    
     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Frontend\FormularioBundle\Entity\Bazar\Producto", inversedBy="postular")
     * @ORM\JoinTable(name="formulario.postular_producto",
     *   joinColumns={
     *     @ORM\JoinColumn(name="postular_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     *   }
     * )

     */
    private $producto;
    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitante;
      
    
    /**
    * @ORM\Column(name="descripcion_otro", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Debe describir otros productos.",groups={"descripcionOtro"})
     */
    private $descripcionOtro;
    

    /**
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "Debe seleccionar al menos una opción de los productos",groups={"Otros"}
     * )
     */
    private $productoOtros;
    
    /**
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "Debe seleccionar al menos una opción de los productos de gastronomía",groups={"Gastronomia"}
     * )
     */
    private $productoGastronomia;
    
    /*
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "Debe seleccionar al menos una opción de los productos",
     * )
     *      */
    
    
    /**
     *
     * @ORM\Column(name="posee_punto", type="boolean", nullable=true)
     */
    private $poseePunto;
    
    /**
    * @ORM\Column(name="descripcion_producto", type="text", nullable=true)
     * @Assert\NotBlank(message="Debe describir otros productos.")
     */
    private $descripcionProducto;
    
    
    /**
    * @ORM\Column(name="cantidad_producto", type="text", nullable=true)
     * @Assert\NotBlank(message="Debe describir la cantidad del o los productos garantizados")
     */
    private $cantidadProducto;
    
    /**
    * @ORM\Column(name="vendedores", type="text", nullable=true)
     * @Assert\NotBlank(message="Debe describir los vendedores.")
     */
    private $vendedores;

    /**
    * @ORM\Column(name="descripcion_marca", type="text", nullable=true)
     * @Assert\NotBlank(message="Debe describir la marca.")
     */
    private $descripcionMarca;
    
    /**
     *
     * @ORM\Column(name="tipoProducto", type="string", length=2, nullable=true)
     */
    private $tipoProducto;

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
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Postular
     */
    public function setSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $solicitante)
    {
        $this->solicitante = $solicitante;
    
        return $this;
    }

    /**
     * Get solicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getSolicitante()
    {
        return $this->solicitante;
    }

    /**
     * Set producto
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $producto
     * @return Postular
     */
    public function setProducto(\Frontend\FormularioBundle\Entity\Bazar\Producto $producto)
    {
        $this->producto = $producto;
    
        return $this;
    }

    /**
     * Get producto
     *
     * @return \Frontend\FormularioBundle\Entity\Bazar\Producto 
     */
    public function getProducto()
    {
        return $this->producto;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->producto = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add producto
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $producto
     * @return Postular
     */
    public function addProducto(\Frontend\FormularioBundle\Entity\Bazar\Producto $producto)
    {
        $this->producto[] = $producto;
    
        return $this;
    }

    /**
     * Remove producto
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $producto
     */
    public function removeProducto(\Frontend\FormularioBundle\Entity\Bazar\Producto $producto)
    {
        $this->producto->removeElement($producto);
    }

    /**
     * Set descripcionOtro
     *
     * @param string $descripcionOtro
     * @return Postular
     */
    public function setDescripcionOtro($descripcionOtro)
    {
        $this->descripcionOtro = $descripcionOtro;
    
        return $this;
    }

    /**
     * Get descripcionOtro
     *
     * @return string 
     */
    public function getDescripcionOtro()
    {
        return $this->descripcionOtro;
    }

    /**
     * Set poseePunto
     *
     * @param boolean $poseePunto
     * @return Postular
     */
    public function setPoseePunto($poseePunto)
    {
        $this->poseePunto = $poseePunto;
    
        return $this;
    }

    /**
     * Get poseePunto
     *
     * @return boolean 
     */
    public function getPoseePunto()
    {
        return $this->poseePunto;
    }

    /**
     * Set descripcionProducto
     *
     * @param string $descripcionProducto
     * @return Postular
     */
    public function setDescripcionProducto($descripcionProducto)
    {
        $this->descripcionProducto = $descripcionProducto;
    
        return $this;
    }

    /**
     * Get descripcionProducto
     *
     * @return string 
     */
    public function getDescripcionProducto()
    {
        return $this->descripcionProducto;
    }

    /**
     * Set vendedores
     *
     * @param string $vendedores
     * @return Postular
     */
    public function setVendedores($vendedores)
    {
        $this->vendedores = $vendedores;
    
        return $this;
    }

    /**
     * Get vendedores
     *
     * @return string 
     */
    public function getVendedores()
    {
        return $this->vendedores;
    }

    /**
     * Set descripcionMarca
     *
     * @param string $descripcionMarca
     * @return Postular
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
     * Set cantidadProducto
     *
     * @param string $cantidadProducto
     * @return Postular
     */
    public function setCantidadProducto($cantidadProducto)
    {
        $this->cantidadProducto = $cantidadProducto;
    
        return $this;
    }

    /**
     * Get cantidadProducto
     *
     * @return string 
     */
    public function getCantidadProducto()
    {
        return $this->cantidadProducto;
    }

    /**
     * Set tipoProducto
     *
     * @param string $tipoProducto
     * @return Postular
     */
    public function setTipoProducto($tipoProducto)
    {
        $this->tipoProducto = $tipoProducto;
    
        return $this;
    }

    /**
     * Get tipoProducto
     *
     * @return string 
     */
    public function getTipoProducto()
    {
        return $this->tipoProducto;
    }

    /**
     * Set productoGastronomia
     *
     * @param string $productoGastronomia
     * @return Postular
     */
    public function setProductoGastronomia($productoGastronomia)
    {
        $this->productoGastronomia = $productoGastronomia;
    
        return $this;
    }

    /**
     * Get productoGastronomia
     *
     * @return string 
     */
    public function getProductoGastronomia()
    {
        return $this->productoGastronomia;
    }

    /**
     * Set productoOtros
     *
     * @param string $productoOtros
     * @return Postular
     */
    public function setProductoOtros($productoOtros)
    {
        $this->productoOtros = $productoOtros;
    
        return $this;
    }

    /**
     * Get productoOtros
     *
     * @return string 
     */
    public function getProductoOtros()
    {
        return $this->productoOtros;
    }

    /**
     * Add productoGastronomia
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $productoGastronomia
     * @return Postular
     */
    public function addProductoGastronomia(\Frontend\FormularioBundle\Entity\Bazar\Producto $productoGastronomia)
    {
        $this->productoGastronomia[] = $productoGastronomia;
    
        return $this;
    }

    /**
     * Remove productoGastronomia
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $productoGastronomia
     */
    public function removeProductoGastronomia(\Frontend\FormularioBundle\Entity\Bazar\Producto $productoGastronomia)
    {
        $this->productoGastronomia->removeElement($productoGastronomia);
    }

    /**
     * Add productoOtros
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $productoOtros
     * @return Postular
     */
    public function addProductoOtros(\Frontend\FormularioBundle\Entity\Bazar\Producto $productoOtros)
    {
        $this->productoOtros[] = $productoOtros;
    
        return $this;
    }

    /**
     * Remove productoOtros
     *
     * @param \Frontend\FormularioBundle\Entity\Bazar\Producto $productoOtros
     */
    public function removeProductoOtros(\Frontend\FormularioBundle\Entity\Bazar\Producto $productoOtros)
    {
        $this->productoOtros->removeElement($productoOtros);
    }
}