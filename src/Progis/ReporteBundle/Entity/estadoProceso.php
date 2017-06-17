<?php

namespace Progis\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class estadoProceso
{
    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\PrincipalBundle\Entity\Miembroespacio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="miembroespacio_id", referencedColumnName="id")
     * })
     * @Assert\Count(min="1",minMessage="Debe seleccionar al menos 1 miembro.")
     */
    private $miembroespacio;
    
    /**
     * @Assert\Count(min="1",minMessage="Debe seleccionar al menos 1 estatus.")
     */
    private $estatusProceso;
    
    /**
     * @Assert\Count(min="1",minMessage="Debe seleccionar al menos 1 tipo.")
     */
    private $tipo;
    
    public function __construct()
    {
        $this->miembroespacio = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set miembroespacio
     *
     * @param \Progis\PrincipalBundle\Entity\Miembroespacio $miembroespacio
     * @return EstadoProceso
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
     * Set estatusProceso
     *
     * @param boolean $estatusProceso
     * @return EstadoProceso
     */
    public function setEstatusProceso($estatusProceso)
    {
        $this->estatusProceso = $estatusProceso;
    
        return $this;
    }

    /**
     * Get estatusProceso
     *
     * @return boolean 
     */
    public function getEstatusProceso()
    {
        return $this->estatusProceso;
    }
    
    /**
     * Set tipo
     *
     * @param boolean $tipo
     * @return EstadoProceso
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return boolean 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}