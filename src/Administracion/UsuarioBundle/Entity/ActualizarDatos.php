<?php

namespace Administracion\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ActualizarDatos
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="usuarios.actualizardatos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "You must be at least {{ limit }}cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }}cm to enter"
     * )
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @ORM\Column(name="extension", type="integer", nullable=false)
     */
    private $extension;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="abreviado", type="string", nullable=true)
     */
    private $abreviado;
    

     /**
     * @Assert\File(
     * maxSize="5M", 
     * maxSizeMessage="El archivo que intenta subir debe ser menos 5Mb") 
     *  
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="string", length=500, nullable=true)
     */
    private $foto;

    /**
     * @var \Nivelorganizacional
     *
     * @ORM\ManyToOne(targetEntity="Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $nivelorganizacional;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\Length(
     *      min = 11,
     *      max = 11,
     *      minMessage = "You must be at least {{ limit }}cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }}cm to enter"
     * )

     */
    private $telefonoLocal;
    
        /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="Los datos introducidos deben ser solo números."
     * )
     * @Assert\Length(
     *      min = 11,
     *      max = 11,
     *      minMessage = "You must be at least {{ limit }}cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }}cm to enter"
     * )
     */
    private $telefonoCelular;
    
        /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $estado;
    
        /**
     * @var \Ciudad
     *
     * @ORM\ManyToOne(targetEntity="Ciudad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ciudad_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $ciudad;

    /**
     * Set extension
     *
     * @param integer $extension
     * @return ActualizarDatos
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * Get extension
     *
     * @return integer 
     */
    public function getExtension()
    {
        return $this->extension;
    }
  /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set abreviado
     *
     * @param string $abreviado
     * @return ActualizarDatos
     */
    public function setAbreviado($abreviado)
    {
        $this->abreviado = $abreviado;
    
        return $this;
    }

    /**
     * Get abreviado
     *
     * @return string 
     */
    public function getAbreviado()
    {
        return $this->abreviado;
    }

    /**
     * Set foto
     *
     * @param string $foto
     * @return ActualizarDatos
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;
    
        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set telefonoLocal
     *
     * @param integer $telefonoLocal
     * @return ActualizarDatos
     */
    public function setTelefonoLocal($telefonoLocal)
    {
        $this->telefonoLocal = $telefonoLocal;
    
        return $this;
    }

    /**
     * Get telefonoLocal
     *
     * @return integer 
     */
    public function getTelefonoLocal()
    {
        return $this->telefonoLocal;
    }

    /**
     * Set telefonoCelular
     *
     * @param integer $telefonoCelular
     * @return ActualizarDatos
     */
    public function setTelefonoCelular($telefonoCelular)
    {
        $this->telefonoCelular = $telefonoCelular;
    
        return $this;
    }

    /**
     * Get telefonoCelular
     *
     * @return integer 
     */
    public function getTelefonoCelular()
    {
        return $this->telefonoCelular;
    }

    /**
     * Set nivelorganizacional
     *
     * @param \Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return ActualizarDatos
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional = null)
    {
        $this->nivelorganizacional = $nivelorganizacional;
    
        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Administracion\UsuarioBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }

    /**
     * Set estado
     *
     * @param \Administracion\UsuarioBundle\Entity\Estado $estado
     * @return ActualizarDatos
     */
    public function setEstado(\Administracion\UsuarioBundle\Entity\Estado $estado = null)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return \Administracion\UsuarioBundle\Entity\Estado 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set ciudad
     *
     * @param \Administracion\UsuarioBundle\Entity\Ciudad $ciudad
     * @return ActualizarDatos
     */
    public function setCiudad(\Administracion\UsuarioBundle\Entity\Ciudad $ciudad = null)
    {
        $this->ciudad = $ciudad;
    
        return $this;
    }

    /**
     * Get ciudad
     *
     * @return \Administracion\UsuarioBundle\Entity\Ciudad 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }
}