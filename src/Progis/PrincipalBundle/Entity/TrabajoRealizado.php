<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 * @ORM\Table(name="progis.trabajorealizado")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo_proceso", type="string")
 * @ORM\DiscriminatorMap({
*               "ticket" = "TrabajoTicket"
*              ,"actividad" = "TrabajoActividad"
*         })
 */


abstract  class TrabajoRealizado
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.trabajorealizado_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", nullable=true)
     */
    private $tipo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;
    
    /**
     * @var string
     *
     * @ORM\Column(name="horas", type="decimal",precision=20, scale= 10, nullable=true)
     */
    private $horas;
    
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\PrincipalBundle\Entity\Miembroespacio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     */
    private $responsable;
    
    

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
     * Set tipo
     *
     * @param string $tipo
     * @return TrabajoRealizado
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return TrabajoRealizado
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set horas
     *
     * @param string $horas
     * @return TrabajoRealizado
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;
    
        return $this;
    }

    /**
     * Get horas
     *
     * @return string 
     */
    public function getHoras()
    {
        return $this->horas;
    }
    
    /**
     * Get responsable
     *
     * @return \Frontend\ProyectoBundle\Entity\Responsable 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
    
    /**
     * Set responsable
     *
     * @param \Frontend\ProyectoBundle\Entity\Responsable $responsable
     * @return Procesar
     */
    public function setResponsable(\Progis\PrincipalBundle\Entity\Miembroespacio $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

}