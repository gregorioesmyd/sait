<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EquiposExternos
 * @UniqueEntity(
 *      fields={"cedula"},
 *      message="La cédula ya existe."
 * )


/**
 * Usuario
 *
 * @ORM\Table(name="controlequipo.usuario")
 * @ORM\Entity
 */
class Usuario
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.usuario_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombres", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="cedula", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value = "0", message="La cédula no puede ser igual a 0.")
     * @Assert\Type(type="digit", message="Debe ser un número.").
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "La cédula o pasaporte debe tener al menos 6 dígitos",
     * )
     */
    private $cedula;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value = "0", message="El teléfono no puede ser igual a 0.")
     * @Assert\Type(type="digit", message="Debe ser un número.").
     * @Assert\Length(
     *      min = 11,
     *      minMessage = "El teléfono debe tener al menos 11 dígitos",
     * )
     * 
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $direccion;




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
     * Set nombres
     *
     * @param string $nombres
     * @return Usuario
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
    
        return $this;
    }

    /**
     * Get nombres
     *
     * @return string 
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return Usuario
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    
        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set cedula
     *
     * @param integer $cedula
     * @return Usuario
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    
        return $this;
    }

    /**
     * Get cedula
     *
     * @return integer 
     */
    public function getCedula()
    {
        return $this->cedula;
    }


    /**
     * Set telefono
     *
     * @param integer $telefono
     * @return Usuario
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    
        return $this;
    }

    /**
     * Get telefono
     *
     * @return integer 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }


    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Usuario
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    
        return $this;
    }

    /**
     * Get direccion
     *
     * @return integer 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }





    public function __toString()
    {
        return $this->getNombres();
    

    }










}
