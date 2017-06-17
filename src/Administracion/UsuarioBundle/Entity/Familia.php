<?php

namespace Administracion\UsuarioBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Table(name="usuarios.familia")
 * @ORM\Entity
 */
class Familia
{
    /**
     * @ORM\Column(name="id_familiar", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idFamiliar;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cedula", type="integer", nullable=false)
     */
    private $cedula;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=false)
     */
    private $fecha_nacimiento;
    
    /**
     * @var string
     *
     * @ORM\Column(name="parentesco", type="string", length=2, nullable=false)
     */
    private $parentesco;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sexo", type="string", length=2, nullable=false)
     */
    private $sexo;
  
    
    /**
     * @var \Usuario
     * @ORM\ManyToOne(targetEntity="Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_trabajador", referencedColumnName="id")
     * })
     */
    private $trabajador;
  
    /**
     * @var string
     *
     * @ORM\Column(name="primer_nombre", type="string", length=255, nullable=false)
     */
    private $primerNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="segundo_nombre", type="string", length=255, nullable=true)
     */
    private $segundoNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="primer_apellido", type="string", length=255, nullable=false)
     */
    private $primerApellido;

    /**
     * @var string
     *
     * @ORM\Column(name="segundo_apellido", type="string", length=255, nullable=true)
     */
    private $segundoApellido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="especial", type="boolean", nullable=true)
     */
    private $especial;
    
    /**
     * @var string
     *
     * @ORM\Column(name="asisteplanvacacional", type="boolean", nullable=true)
     */
    private $asisteplanvacacional;
    
    /**
     * @var string
     *
     * @ORM\Column(name="asisteTardeRecreativa", type="boolean", nullable=true)
     */
    private $asisteTardeRecreativa;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alerenfer", type="text", nullable=true)
     */
    private $alerenfer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tlfcelular1", type="string", nullable=true)
     */
    private $tlfcelular1;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tlfcelular2", type="string", nullable=true)
     */
    private $tlfcelular2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tallaCamisa", type="string", length=2, nullable=true)
     */
    private $tallaCamisa;
    
    /**
     * @var integer
     * @ORM\Column(name="tallaZapatos", type="integer", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 2,
     *      minMessage = "You must be at least {{ limit }}cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }}cm to enter"
     * )
     */
    private $tallaZapatos;




    /**
     * Set idFamiliar
     *
     * @param integer $idFamiliar
     * @return Familiar
     */
    public function setIdFamiliar($idFamiliar)
    {
        $this->idFamiliar = $idFamiliar;

        return $this;
    }

    /**
     * Get idFamiliar
     *
     * @return integer 
     */
    public function getIdFamiliar()
    {
        return $this->idFamiliar;
    }

    /**
     * Set cedula
     *
     * @param integer $cedula
     * @return Familia
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
     * Set fecha_nacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Familia
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fecha_nacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fecha_nacimiento
     *
     * @return \DateTime 
     */
    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }

    /**
     * Set parentesco
     *
     * @param string $parentesco
     * @return Familia
     */
    public function setParentesco($parentesco)
    {
        $this->parentesco = $parentesco;

        return $this;
    }

    /**
     * Get parentesco
     *
     * @return string 
     */
    public function getParentesco()
    {
        return $this->parentesco;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     * @return Familia
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set primerNombre
     *
     * @param string $primerNombre
     * @return Familia
     */
    public function setPrimerNombre($primerNombre)
    {
        $this->primerNombre = $primerNombre;

        return $this;
    }

    /**
     * Get primerNombre
     *
     * @return string 
     */
    public function getPrimerNombre()
    {
        return $this->primerNombre;
    }

    /**
     * Set segundoNombre
     *
     * @param string $segundoNombre
     * @return Familia
     */
    public function setSegundoNombre($segundoNombre)
    {
        $this->segundoNombre = $segundoNombre;

        return $this;
    }

    /**
     * Get segundoNombre
     *
     * @return string 
     */
    public function getSegundoNombre()
    {
        return $this->segundoNombre;
    }

    /**
     * Set primerApellido
     *
     * @param string $primerApellido
     * @return Familia
     */
    public function setPrimerApellido($primerApellido)
    {
        $this->primerApellido = $primerApellido;

        return $this;
    }

    /**
     * Get primerApellido
     *
     * @return string 
     */
    public function getPrimerApellido()
    {
        return $this->primerApellido;
    }

    /**
     * Set segundoApellido
     *
     * @param string $segundoApellido
     * @return Familia
     */
    public function setSegundoApellido($segundoApellido)
    {
        $this->segundoApellido = $segundoApellido;

        return $this;
    }

    /**
     * Get segundoApellido
     *
     * @return string 
     */
    public function getSegundoApellido()
    {
        return $this->segundoApellido;
    }

    /**
     * Set especial
     *
     * @param boolean $especial
     * @return Familia
     */
    public function setEspecial($especial)
    {
        $this->especial = $especial;

        return $this;
    }

    /**
     * Get especial
     *
     * @return boolean 
     */
    public function getEspecial()
    {
        return $this->especial;
    }

    /**
     * Set asisteplanvacacional
     *
     * @param boolean $asisteplanvacacional
     * @return Familia
     */
    public function setAsisteplanvacacional($asisteplanvacacional)
    {
        $this->asisteplanvacacional = $asisteplanvacacional;

        return $this;
    }

    /**
     * Get asisteplanvacacional
     *
     * @return boolean 
     */
    public function getAsisteplanvacacional()
    {
        return $this->asisteplanvacacional;
    }



    /**
     * Set trabajador
     *
     * @param \BaseDatos\UsuarioBundle\Entity\Perfil $trabajador
     * @return Familia
     */
    public function setTrabajador(\BaseDatos\UsuarioBundle\Entity\Perfil $trabajador = null)
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    /**
     * Get trabajador
     *
     * @return \BaseDatos\UsuarioBundle\Entity\Perfil 
     */
    public function getTrabajador()
    {
        return $this->trabajador;
    }

    /**
     * Set alerenfer
     *
     * @param string $alerenfer
     * @return Familia
     */
    public function setAlerenfer($alerenfer)
    {
        $this->alerenfer = $alerenfer;
    
        return $this;
    }

    /**
     * Get alerenfer
     *
     * @return string 
     */
    public function getAlerenfer()
    {
        return $this->alerenfer;
    }

    /**
     * Set tlfcelular1
     *
     * @param string $tlfcelular1
     * @return Familia
     */
    public function setTlfcelular1($tlfcelular1)
    {
        $this->tlfcelular1 = $tlfcelular1;
    
        return $this;
    }

    /**
     * Get tlfcelular1
     *
     * @return string 
     */
    public function getTlfcelular1()
    {
        return $this->tlfcelular1;
    }

    /**
     * Set tlfcelular2
     *
     * @param string $tlfcelular2
     * @return Familia
     */
    public function setTlfcelular2($tlfcelular2)
    {
        $this->tlfcelular2 = $tlfcelular2;
    
        return $this;
    }

    /**
     * Get tlfcelular2
     *
     * @return string 
     */
    public function getTlfcelular2()
    {
        return $this->tlfcelular2;
    }

    /**
     * Set asisteTardeRecreativa
     *
     * @param boolean $asisteTardeRecreativa
     * @return Familia
     */
    public function setAsisteTardeRecreativa($asisteTardeRecreativa)
    {
        $this->asisteTardeRecreativa = $asisteTardeRecreativa;
    
        return $this;
    }

    /**
     * Get asisteTardeRecreativa
     *
     * @return boolean 
     */
    public function getAsisteTardeRecreativa()
    {
        return $this->asisteTardeRecreativa;
    }

    /**
     * Set tallaCamisa
     *
     * @param string $tallaCamisa
     * @return Familia
     */
    public function setTallaCamisa($tallaCamisa)
    {
        $this->tallaCamisa = $tallaCamisa;
    
        return $this;
    }

    /**
     * Get tallaCamisa
     *
     * @return string 
     */
    public function getTallaCamisa()
    {
        return $this->tallaCamisa;
    }

    /**
     * Set tallaZapatos
     *
     * @param integer $tallaZapatos
     * @return Familia
     */
    public function setTallaZapatos($tallaZapatos)
    {
        $this->tallaZapatos = $tallaZapatos;
    
        return $this;
    }

    /**
     * Get tallaZapatos
     *
     * @return integer 
     */
    public function getTallaZapatos()
    {
        return $this->tallaZapatos;
    }
}