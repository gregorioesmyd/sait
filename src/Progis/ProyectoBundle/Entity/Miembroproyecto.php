<?php

namespace Progis\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.miembroproyecto")
 * @ORM\Entity
 */
class Miembroproyecto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.miembroproyecto_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

   
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Proyecto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")
     * })
     */
    private $proyecto;
    
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\PrincipalBundle\Entity\Miembroespacio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="miembroespacio_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un usuario.")
     */
    private $miembroespacio;
    
    
    /**
     * @var \rol
     *
     * @ORM\ManyToOne(targetEntity="Rolproyecto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rolproyecto_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un rol.")
     */
    private $rolproyecto;

    /**
     * @var integer
     *
     * @ORM\Column(name="liderproyecto", type="boolean", nullable=true)
     */
    
    private $liderproyecto;
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
     * Set proyecto
     *
     * @param \Progis\ProyectoBundle\Entity\Proyecto $proyecto
     * @return Miembroproyecto
     */
    public function setProyecto(\Progis\ProyectoBundle\Entity\Proyecto $proyecto = null)
    {
        $this->proyecto = $proyecto;
    
        return $this;
    }

    /**
     * Get proyecto
     *
     * @return \Progis\ProyectoBundle\Entity\Proyecto 
     */
    public function getProyecto()
    {
        return $this->proyecto;
    }

    /**
     * Set miembroespacio
     *
     * @param \Progis\PrincipalBundle\Entity\Miembroespacio $miembroespacio
     * @return Miembroproyecto
     */
    public function setMiembroespacio(\Progis\PrincipalBundle\Entity\Miembroespacio $miembroespacio = null)
    {
        $this->miembroespacio = $miembroespacio;
    
        return $this;
    }

    /**
     * Get miembroespacio
     *
     * @return \Progis\PrincipalBundle\Entity\Miembroespacio 
     */
    public function getMiembroespacio()
    {
        return $this->miembroespacio;
    }



    /**
     * Set rolproyecto
     *
     * @param \Progis\PrincipalBundle\Entity\Rolproyectoprogis $rolproyecto
     * @return Miembroproyecto
     */
    public function setRolproyecto(\Progis\ProyectoBundle\Entity\Rolproyecto $rolproyecto = null)
    {
        $this->rolproyecto = $rolproyecto;
    
        return $this;
    }

    /**
     * Get rolproyecto
     *
     * @return \Progis\PrincipalBundle\Entity\Rolproyecto
     */
    public function getRolproyecto()
    {
        return $this->rolproyecto;
    }
    
    public function setLiderproyecto($liderproyecto)
    {
        $this->liderproyecto = $liderproyecto;
    
        return $this;
    }

    /**
     * Get liderproyecto
     *
     * @return string 
     */
    public function getLiderproyecto()
    {
        return $this->liderproyecto;
    }
    
    public function __toString() {
        return $this->getMiembroespacio()->getUsuario()->getPrimerNombre()." ".$this->getMiembroespacio()->getUsuario()->getPrimerApellido();
    }
}