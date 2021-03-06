<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.metas")
 * @ORM\Entity
 */


class Metas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.metas_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

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
     * @var string
     *
     * @ORM\Column(name="horas", type="integer", nullable=true)
     */
    private $horas;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechadesde", type="date", nullable=false)
     * @Assert\NotBlank(message="Debe seleccionar la fecha desde.")
     */
    private $fechadesde;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechahasta", type="date", nullable=false)
     * @Assert\NotBlank(message="Debe seleccionar la fecha hasta.")
     */
    private $fechahasta;
   
    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    private $observacion;

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
     * @var \Frontend\PrincipalBundle\Entity\Estatus
     * @ORM\ManyToOne(targetEntity="Estatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estatus_id", referencedColumnName="id")
     * })
     */
    private $estatus;
    
    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="integer", nullable=true)
     */
    private $porcentaje;
    
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
     * Set horas
     *
     * @param integer $horas
     * @return Metas
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;
    
        return $this;
    }

    /**
     * Get horas
     *
     * @return integer 
     */
    public function getHoras()
    {
        return $this->horas;
    }

    /**
     * Set fechadesde
     *
     * @param \DateTime $fechadesde
     * @return Metas
     */
    public function setFechadesde($fechadesde)
    {
        $this->fechadesde = $fechadesde;
    
        return $this;
    }

    /**
     * Get fechadesde
     *
     * @return \DateTime 
     */
    public function getFechadesde()
    {
        return $this->fechadesde;
    }

    /**
     * Set fechahasta
     *
     * @param \DateTime $fechahasta
     * @return Metas
     */
    public function setFechahasta($fechahasta)
    {
        $this->fechahasta = $fechahasta;
    
        return $this;
    }

    /**
     * Get fechahasta
     *
     * @return \DateTime 
     */
    public function getFechahasta()
    {
        return $this->fechahasta;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Metas
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set miembroespacio
     *
     * @param \Progis\PrincipalBundle\Entity\Miembroespacio $miembroespacio
     * @return Metas
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
     * Set estatus
     *
     * @param \Progis\PrincipalBundle\Entity\Estatus $estatus
     * @return Metas
     */
    public function setEstatus(\Progis\PrincipalBundle\Entity\Estatus $estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return \Progis\PrincipalBundle\Entity\Estatus 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }



    /**
     * Set porcentaje
     *
     * @param integer $porcentaje
     * @return Metas
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;
    
        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return integer 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }
}