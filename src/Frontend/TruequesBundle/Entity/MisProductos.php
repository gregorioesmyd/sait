<?php

namespace Frontend\TruequesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Frontend\TruequesBundle\Entity\Producto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MisProductos
 *
 * @ORM\Table(name="trueques.mis_producto")
 * @ORM\Entity(repositoryClass="Frontend\TruequesBundle\Entity\MisProductosRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class MisProductos
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
     * @var \Status
     *
     *
     * @ORM\ManyToOne(targetEntity="Frontend\TruequesBundle\Entity\Status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $status;

    /**
     * @var \Perfil
     *
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;

    /**
     * @Assert\NotBlank(message="Debe seleccionar un producto.")
     * @Assert\Type(
     *     type="Frontend\TruequesBundle\Entity\Producto",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\ManyToOne(targetEntity="Frontend\TruequesBundle\Entity\Producto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     */
    private $productoCambiar;

    /**
     * @var Date
     * @ORM\Column(name="fecha_creacion", type="date", nullable=true)
     */
    private $fecha_creacion;

    /**
     * @var Date
     * @ORM\Column(name="fecha_fin_publicacion", type="date", nullable=true)
     */
    private $fecha_fin_publicacion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Frontend\TruequesBundle\Entity\Producto")
     * @ORM\JoinTable(name="trueques.mis_producto_interes",
     *   joinColumns={
     *     @ORM\JoinColumn(name="misproducto_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "Debe seleccionar al menos un producto de interés",
     * )
     */
    private $mis_producto_interes;

    /**
     * @ORM\ManyToMany(targetEntity="MisProductos", mappedBy="interesados")
     */
    private $mi_producto;

    /**
     * @ORM\ManyToMany(targetEntity="MisProductos", inversedBy="mi_producto")
     * @ORM\JoinTable(name="trueques.interesados_trueques",
     *      joinColumns={@ORM\JoinColumn(name="mi_producto_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="interesado_id", referencedColumnName="id")}
     *      )
     */
    private $interesados;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\TruequesBundle\Entity\Trueque", mappedBy="misproducto_user1")
     **/
    private $trueques_parent;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\TruequesBundle\Entity\Trueque", mappedBy="misproducto_user2")
     **/
    private $trueques_child;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mis_producto_interes = new ArrayCollection();
        $this->mi_producto = new ArrayCollection();
        $this->interesados = new ArrayCollection();
        $this->trueques_parent = new ArrayCollection();
        $this->trueques_child = new ArrayCollection();
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
     * Set productoCambiar
     *
     * @param \Frontend\TruequesBundle\Entity\Producto $productoCambiar
     * @return MisProductos
     */
    public function setProductoCambiar(\Frontend\TruequesBundle\Entity\Producto $productoCambiar = null)
    {
        $this->productoCambiar = $productoCambiar;
    
        return $this;
    }

    /**
     * Get productoCambiar
     *
     * @return \Frontend\TruequesBundle\Entity\Producto 
     */
    public function getProductoCambiar()
    {
        return $this->productoCambiar;
    }

    /**
     * Add mis_producto_interes
     *
     * @param \Frontend\TruequesBundle\Entity\Producto $misProductoInteres
     * @return MisProductos
     */
    public function addMisProductoIntere(\Frontend\TruequesBundle\Entity\Producto $misProductoInteres)
    {
        $this->mis_producto_interes[] = $misProductoInteres;
    
        return $this;
    }

    /**
     * Remove mis_producto_interes
     *
     * @param \Frontend\TruequesBundle\Entity\Producto $misProductoInteres
     */
    public function removeMisProductoIntere(\Frontend\TruequesBundle\Entity\Producto $misProductoInteres)
    {
        $this->mis_producto_interes->removeElement($misProductoInteres);
    }

    /**
     * Get mis_producto_interes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMisProductoInteres()
    {
        return $this->mis_producto_interes;
    }

    /**
     * Set user
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $user
     * @return MisProductos
     */
    public function setUser(\Administracion\UsuarioBundle\Entity\Perfil $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return MisProductos
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add mi_producto
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $miProducto
     * @return MisProductos
     */
    public function addMiProducto(\Frontend\TruequesBundle\Entity\MisProductos $miProducto)
    {
        $this->mi_producto[] = $miProducto;
    
        return $this;
    }

    /**
     * Remove mi_producto
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $miProducto
     */
    public function removeMiProducto(\Frontend\TruequesBundle\Entity\MisProductos $miProducto)
    {
        $this->mi_producto->removeElement($miProducto);
    }

    /**
     * Get mi_producto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMiProducto()
    {
        return $this->mi_producto;
    }

    /**
     * Add interesados
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $interesados
     * @return MisProductos
     */
    public function addInteresado(\Frontend\TruequesBundle\Entity\MisProductos $interesados)
    {
        $this->interesados[] = $interesados;
    
        return $this;
    }

    /**
     * Remove interesados
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $interesados
     */
    public function removeInteresado(\Frontend\TruequesBundle\Entity\MisProductos $interesados)
    {
        $this->interesados->removeElement($interesados);
    }

    /**
     * Get interesados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInteresados()
    {
        return $this->interesados;
    }

    /**
     * Add trueques_parent
     *
     * @param \Frontend\TruequesBundle\Entity\Trueque $truequesParent
     * @return MisProductos
     */
    public function addTruequesParent(\Frontend\TruequesBundle\Entity\Trueque $truequesParent)
    {
        $this->trueques_parent[] = $truequesParent;
    
        return $this;
    }

    /**
     * Remove trueques_parent
     *
     * @param \Frontend\TruequesBundle\Entity\Trueque $truequesParent
     */
    public function removeTruequesParent(\Frontend\TruequesBundle\Entity\Trueque $truequesParent)
    {
        $this->trueques_parent->removeElement($truequesParent);
    }

    /**
     * Get trueques_parent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTruequesParent()
    {
        return $this->trueques_parent;
    }

    /**
     * Add trueques_child
     *
     * @param \Frontend\TruequesBundle\Entity\Trueque $truequesChild
     * @return MisProductos
     */
    public function addTruequesChild(\Frontend\TruequesBundle\Entity\Trueque $truequesChild)
    {
        $this->trueques_child[] = $truequesChild;
    
        return $this;
    }

    /**
     * Remove trueques_child
     *
     * @param \Frontend\TruequesBundle\Entity\Trueque $truequesChild
     */
    public function removeTruequesChild(\Frontend\TruequesBundle\Entity\Trueque $truequesChild)
    {
        $this->trueques_child->removeElement($truequesChild);
    }

    /**
     * Get trueques_child
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTruequesChild()
    {
        return $this->trueques_child;
    }

    /**
     * @ORM\PrePersist
     */
    public function setFechaCreacionValue()
    {
        $this->FechaCreacion = new \DateTime();
    }

    /**
     * Set fecha_creacion
     *
     * @param \DateTime $fechaCreacion
     * @return MisProductos
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fecha_creacion = $fechaCreacion;
    
        return $this;
    }

    /**
     * Get fecha_creacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * Set fecha_fin_publicacion
     *
     * @param \DateTime $fechaFinPublicacion
     * @return MisProductos
     */
    public function setFechaFinPublicacion($fechaFinPublicacion)
    {
        $this->fecha_fin_publicacion = $fechaFinPublicacion;
    
        return $this;
    }

    /**
     * Get fecha_fin_publicacion
     *
     * @return \DateTime 
     */
    public function getFechaFinPublicacion()
    {
        return $this->fecha_fin_publicacion;
    }
}