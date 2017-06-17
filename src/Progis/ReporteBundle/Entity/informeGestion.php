<?php

namespace Progis\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class informeGestion
{
    /**
     * @Assert\NotBlank(message="Debe seleccionar un año.")
     */
    private $anio;
    
    /**
     * @Assert\NotBlank(message="Debe seleccionar un mes.")
     */
    private $mes;
    
    /**
     * Set anio
     *
     * @param boolean $anio
     * @return EstadoProceso
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;
    
        return $this;
    }

    /**
     * Get anio
     *
     * @return boolean 
     */
    public function getAnio()
    {
        return $this->anio;
    }
    
    /**
     * Set mes
     *
     * @param boolean $mes
     * @return EstadoProceso
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    
        return $this;
    }

    /**
     * Get mes
     *
     * @return boolean 
     */
    public function getMes()
    {
        return $this->mes;
    }
}