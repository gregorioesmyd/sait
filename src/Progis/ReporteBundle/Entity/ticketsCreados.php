<?php

namespace Progis\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Objetivo
 *
 */
class ticketsCreados
{
    
     /**
     * @var \Frontend\ProyectoBundle\Entity\Actividad
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @Assert\NotBlank(message="Debe seleccionar un usuario.")
     */
    private $usuario;
    
    /**
     * @Assert\NotBlank(message="La fecha de inicio estimada no puede estar en blanco.")
     */
    private $desde;
    
    /**
     * @Assert\NotBlank(message="La fecha de inicio estimada no puede estar en blanco.")
     */
    private $hasta;


    
    

    /**
     * Set usuario
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $usuario
     * @return ticketsCreados
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
     * Set desde
     *
     * @param integer $desde
     * @return ticketsCreados
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;
    
        return $this;
    }

    /**
     * Get desde
     *
     * @return integer 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param integer $hasta
     * @return ticketsCreados
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;
    
        return $this;
    }

    /**
     * Get hasta
     *
     * @return integer 
     */
    public function getHasta()
    {
        return $this->hasta;
    }
}