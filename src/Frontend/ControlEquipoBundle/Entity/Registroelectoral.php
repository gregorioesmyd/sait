<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuario
 *
 * @ORM\Table(name="controlequipo.registroelectoral")
 */
class Registroelectoral
{
 
    /**
     * @var string
     *
     * @ORM\Column(name="nacionalidad", type="string", length=2, nullable=true)
     * @Assert\NotBlank()
     */
    private $nacionalidad;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="primernombre", type="string", length=50, nullable=true)
     * @Assert\NotBlank()
     */
    private $primernombre;
    /**
     * @var string
     *
     * @ORM\Column(name="segundonombre", type="string", length=50, nullable=true)
     * @Assert\NotBlank()
     */
    private $segundonombre;

    /**
     * @var string
     *
     * @ORM\Column(name="perimerapellido", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $primerapellido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="segundoapellido", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $segundoapellido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codcen", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $codcen;


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
     * Set primernombre
     *
     * @param string $primernombre
     * @return Usuario
     */
    public function setPrimernombre($primernombre)
    {
        $this->primernombre = $primernombre;
    
        return $this;
    }

    /**
     * Get primernombre
     *
     * @return string 
     */
    public function getPrimernombre()
    {
        return $this->primernombre;
    }
    
    /**
     * Set segundonombre
     *
     * @param string $segundonombre
     * @return Usuario
     */
    public function setSegundonombre($segundonombre)
    {
        $this->segundonombre = $segundonombre;
    
        return $this;
    }

    /**
     * Get segundonombre
     *
     * @return string 
     */
    public function getSegundonombre()
    {
        return $this->segundonombre;
    }


    /**
     * Set primerapellido
     *
     * @param string $primerapellido
     * @return Usuario
     */
    public function setPrimerapellido($primerapellido)
    {
        $this->primerapellido = $primerapellido;
    
        return $this;
    }

    /**
     * Get primerapellido
     *
     * @return string 
     */
    public function getPrimerapellido()
    {
        return $this->primerapellido;
    }
    
    /**
     * Set segundoapellido
     *
     * @param string $segundoapellido
     * @return Usuario
     */
    public function setSegundoapellido($segundoapellido)
    {
        $this->segundoapellido = $segundoapellido;
    
        return $this;
    }

    /**
     * Get segundoapellido
     *
     * @return string 
     */
    public function getSegundoapellido()
    {
        return $this->segundoapellido;
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
     * Set nacionalidad
     *
     * @param integer $nacionalidad
     * @return Usuario
     */
    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;
    
        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return integer 
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }
    
    /**
     * Set codcen
     *
     * @param integer $codcen
     * @return Usuario
     */
    public function setCodcen($codcen)
    {
        $this->codcen = $codcen;
    
        return $this;
    }

    /**
     * Get codcen
     *
     * @return integer 
     */
    public function getCodcen()
    {
        return $this->codcen;
    }



   

    public function __toString()
    {
        return $this->getNombres();
    

    }










}
