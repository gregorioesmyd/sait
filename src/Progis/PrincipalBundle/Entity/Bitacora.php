<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 */
class Bitacora
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.bitacora_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

    /**
     * @var string
     *
     * @ORM\Column(name="idmodulo", type="integer", nullable=false)
     */
    private $idmodulo;
    

    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Modulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modulo_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $modulo;
    
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $nivelorganizacional;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechahora", type="datetime", nullable=false)
     */
    private $fechahora;

     /**
     * @var \Frontend\ProyectoBundle\Entity\Actividad
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     */
   

    private $responsable;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="La descripcion del proyecto no puede estar en blanco.")
     */
    private $descripcion;

    

    public function getId()
    {
        return $this->id;
    }
    
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    public function setIdmodulo($idmodulo)
    {
        $this->idmodulo = $idmodulo;
    
        return $this;
    }

    public function getIdmodulo()
    {
        return $this->idmodulo;
    }
    
    public function setModulo(\Progis\PrincipalBundle\Entity\Modulo $modulo = null)
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get modulo
     *
     * @return \Frontend\ModuloBundle\Entity\Modulo 
     */
    public function getModulo()
    {
        return $this->modulo;
    }
    
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional = null)
    {
        $this->nivelorganizacional = $nivelorganizacional;

        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Frontend\NivelorganizacionalBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }
    
     
    public function setFechahora($fechahora)
    {
        $this->fechahora = $fechahora;

        return $this;
    }

    /**
     * Get fechahora
     *
     * @return string 
     */
    public function getFechahora()
    {
        return $this->fechahora;
    }

    /**
     * Set responsableId
     *
     * @param integer $responsableId
     * @return Tarea
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsableId
     *
     * @return integer 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }


    public function __toString()
    {
        return $this->getComentario();
    }
}