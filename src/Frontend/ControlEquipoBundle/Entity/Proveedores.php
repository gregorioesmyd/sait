<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proveedores
 *
 * @ORM\Table(name="controlequipo.proveedores")
 * @ORM\Entity
 */
class Proveedores
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.proveedores_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_proveedor", type="string", length=255, nullable=false)
     */
    private $descripcionProveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="rif", type="string", length=20, nullable=true)
     */
    private $rif;



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
     * Set descripcionProveedor
     *
     * @param string $descripcionProveedor
     * @return Proveedores
     */
    public function setDescripcionProveedor($descripcionProveedor)
    {
        $this->descripcionProveedor = $descripcionProveedor;
    
        return $this;
    }

    /**
     * Get descripcionProveedor
     *
     * @return string 
     */
    public function getDescripcionProveedor()
    {
        return $this->descripcionProveedor;
    }
    
    public function __toString() {
        return $this->descripcionProveedor;
    }

    /**
     * Set rif
     *
     * @param string $rif
     * @return Proveedores
     */
    public function setRif($rif)
    {
        $this->rif = $rif;
    
        return $this;
    }

    /**
     * Get rif
     *
     * @return string 
     */
    public function getRif()
    {
        return $this->rif;
    }
}