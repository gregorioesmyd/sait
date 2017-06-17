<?php

namespace Frontend\TruequesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trueque
 *
 * @ORM\Table(name="trueques.intercambio_trueque", uniqueConstraints={@ORM\UniqueConstraint(name="fk_unique_products_user", columns={"misproducto_user1_id", "misproducto_user2_id"})})
 * @ORM\Entity(repositoryClass="Frontend\TruequesBundle\Entity\TruequeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Trueque
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
     *   @ORM\JoinColumn(name="status_user1_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $status_user1;

    /**
     * @var \Status
     *
     *
     * @ORM\ManyToOne(targetEntity="Frontend\TruequesBundle\Entity\Status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_user2_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $status_user2;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\TruequesBundle\Entity\MisProductos", inversedBy="trueques_parent")
     * @ORM\JoinColumn(name="misproducto_user1_id", referencedColumnName="id")
     */
    private $mis_producto_user1;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\TruequesBundle\Entity\MisProductos", inversedBy="trueques_child")
     * @ORM\JoinColumn(name="misproducto_user2_id", referencedColumnName="id")
     */
    private $mis_producto_user2;

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
     * Set status_user1
     *
     * @param \Frontend\TruequesBundle\Entity\Status $statusUser1
     * @return Trueque
     */
    public function setStatusUser1(\Frontend\TruequesBundle\Entity\Status $statusUser1 = null)
    {
        $this->status_user1 = $statusUser1;
    
        return $this;
    }

    /**
     * Get status_user1
     *
     * @return \Frontend\TruequesBundle\Entity\Status 
     */
    public function getStatusUser1()
    {
        return $this->status_user1;
    }

    /**
     * Set status_user2
     *
     * @param \Frontend\TruequesBundle\Entity\Status $statusUser2
     * @return Trueque
     */
    public function setStatusUser2(\Frontend\TruequesBundle\Entity\Status $statusUser2 = null)
    {
        $this->status_user2 = $statusUser2;
    
        return $this;
    }

    /**
     * Get status_user2
     *
     * @return \Frontend\TruequesBundle\Entity\Status 
     */
    public function getStatusUser2()
    {
        return $this->status_user2;
    }

    /**
     * Set mis_producto_user1
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $misProductoUser1
     * @return Trueque
     */
    public function setMisProductoUser1(\Frontend\TruequesBundle\Entity\MisProductos $misProductoUser1 = null)
    {
        $this->mis_producto_user1 = $misProductoUser1;
    
        return $this;
    }

    /**
     * Get mis_producto_user1
     *
     * @return \Frontend\TruequesBundle\Entity\MisProductos 
     */
    public function getMisProductoUser1()
    {
        return $this->mis_producto_user1;
    }

    /**
     * Set mis_producto_user2
     *
     * @param \Frontend\TruequesBundle\Entity\MisProductos $misProductoUser2
     * @return Trueque
     */
    public function setMisProductoUser2(\Frontend\TruequesBundle\Entity\MisProductos $misProductoUser2 = null)
    {
        $this->mis_producto_user2 = $misProductoUser2;
    
        return $this;
    }

    /**
     * Get mis_producto_user2
     *
     * @return \Frontend\TruequesBundle\Entity\MisProductos 
     */
    public function getMisProductoUser2()
    {
        return $this->mis_producto_user2;
    }
}