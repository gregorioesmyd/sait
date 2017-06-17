<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.miembroespacio")
 * @ORM\Entity
 */
class Miembroespacio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.miembroespacio_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

   
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
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Jornadalaboral")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="jornadalaboral_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar menos una jornada.")
     */
    private $jornadalaboral;
    
     /**
     * @var \Frontend\ProyectoBundle\Entity\Actividad
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un usuario.")
     */
    private $usuario;
    
     /**
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     */
    private $responsable;
    
     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Rolgeneral", inversedBy="miembroespacio")
     * @ORM\JoinTable(name="progis.rolmiembroespacio",
     *   joinColumns={
     *     @ORM\JoinColumn(name="miembroespacio_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="rolgeneral_id", referencedColumnName="id")
     *   }
     * )
     */
    private $rolgeneral;
    
    /**
    * @ORM\Column(name="activo", type="boolean", nullable=true)
    */
    private $activo;
    
    /**
    * @ORM\Column(name="mostrar_en_reporte", type="boolean", nullable=true)
    */
    private $mostrarEnReporte;
    
    /**
    * @ORM\Column(name="poseerolgeneral", type="boolean", nullable=true)
    */
    private $poseerolgeneral;
   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rolgeneral = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() {
        return ucfirst(strtolower($this->getUsuario()->getPrimerNombre())).' '.ucfirst(strtolower($this->getUsuario()->getPrimerApellido()));
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
     * Set activo
     *
     * @param boolean $activo
     * @return Miembroespacio
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set poseerolgeneral
     *
     * @param boolean $poseerolgeneral
     * @return Miembroespacio
     */
    public function setPoseerolgeneral($poseerolgeneral)
    {
        $this->poseerolgeneral = $poseerolgeneral;
    
        return $this;
    }

    /**
     * Get poseerolgeneral
     *
     * @return boolean 
     */
    public function getPoseerolgeneral()
    {
        return $this->poseerolgeneral;
    }

    /**
     * Set nivelorganizacional
     *
     * @param \Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return Miembroespacio
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional)
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
     * Set jornadalaboral
     *
     * @param \Progis\PrincipalBundle\Entity\Jornadalaboral $jornadalaboral
     * @return Miembroespacio
     */
    public function setJornadalaboral(\Progis\PrincipalBundle\Entity\Jornadalaboral $jornadalaboral = null)
    {
        $this->jornadalaboral = $jornadalaboral;
    
        return $this;
    }

    /**
     * Get jornadalaboral
     *
     * @return \Progis\PrincipalBundle\Entity\Jornadalaboral 
     */
    public function getJornadalaboral()
    {
        return $this->jornadalaboral;
    }

    /**
     * Set usuario
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $usuario
     * @return Miembroespacio
     */
    public function setUsuario(\Administracion\UsuarioBundle\Entity\Perfil $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add rolgeneral
     *
     * @param \Progis\PrincipalBundle\Entity\Rolgeneral $rolgeneral
     * @return Miembroespacio
     */
    public function addRolgeneral(\Progis\PrincipalBundle\Entity\Rolgeneral $rolgeneral)
    {
        $this->rolgeneral[] = $rolgeneral;
    
        return $this;
    }

    /**
     * Remove rolgeneral
     *
     * @param \Progis\PrincipalBundle\Entity\Rolgeneral $rolgeneral
     */
    public function removeRolgeneral(\Progis\PrincipalBundle\Entity\Rolgeneral $rolgeneral)
    {
        $this->rolgeneral->removeElement($rolgeneral);
    }

    /**
     * Get rolgeneral
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRolgeneral()
    {
        return $this->rolgeneral;
    }

    /**
     * Set responsable
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsable
     * @return Miembroespacio
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;
    
        return $this;
    }

    /**
     * Get responsable
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set mostrarEnReporte
     *
     * @param boolean $mostrarEnReporte
     * @return Miembroespacio
     */
    public function setMostrarEnReporte($mostrarEnReporte)
    {
        $this->mostrarEnReporte = $mostrarEnReporte;
    
        return $this;
    }

    /**
     * Get mostrarEnReporte
     *
     * @return boolean 
     */
    public function getMostrarEnReporte()
    {
        return $this->mostrarEnReporte;
    }
}