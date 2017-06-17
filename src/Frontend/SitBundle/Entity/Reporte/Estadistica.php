<?php

namespace Frontend\SitBundle\Entity\Reporte;
use Symfony\Component\Validator\Constraints as Assert;

class Estadistica
{
    
    /**
     * @Assert\NotBlank(message="Debe escribir la fecha desde.")
     */
    private $fechadesde;
    
    /**
     * @Assert\NotBlank(message="Debe escribir la fecha hasta.")
     */
    private $fechahasta;
    
    /**
     * @Assert\Count(min="1",minMessage="Debe seleccionar al menos 1 usuario.")
     */
    private $usuario;

    
    public function setFechadesde($fechadesde)
    {
        $this->fechadesde = $fechadesde;
    
        return $this;
    }

    public function getFechadesde()
    {
        return $this->fechadesde;
    }

    public function setFechahasta($fechahasta)
    {
        $this->fechahasta = $fechahasta;
    
        return $this;
    }

    public function getFechahasta()
    {
        return $this->fechahasta;
    }

    
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

}

