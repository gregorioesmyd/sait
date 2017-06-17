<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prestamo
 *
 * @ORM\Table(name="videoteca.prestamo")
 * @ORM\Entity
 */
class Prestamo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="user_solicitante", referencedColumnName="id", nullable=false)
     * 
     * })
     * 
     */
    private $userSolicitante;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="user_prestamista", referencedColumnName="id", nullable=false)
     * 
     * })
     * 
     */
    private $userPrestamista;

    /**
     * @var string
     *
     * @ORM\Column(name="factura", type="string", length=100)
     * @Assert\NotNull(message="Debe de indicar el numero de factura")
     */
    private $factura;

    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="user_devolucion", referencedColumnName="id", nullable=true)
     * 
     * })
     * 
     */
    private $userDevolucion;

   
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
     * Set userSolicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $userSolicitante
     * @return Prestamo
     */
    public function setUserSolicitante(\Administracion\UsuarioBundle\Entity\Perfil$userSolicitante)
    {
        $this->userSolicitante = $userSolicitante;
    
        return $this;
    }

    /**
     * Get userSolicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getUserSolicitante()
    {
        return $this->userSolicitante;
    }

    /**
     * Set userPrestamista
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $userPrestamista
     * @return Prestamo
     */
    public function setUserPrestamista(\Administracion\UsuarioBundle\Entity\Perfil $userPrestamista)
    {
        $this->userPrestamista = $userPrestamista;
    
        return $this;
    }

    /**
     * Get userPrestamista
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil
     */
    public function getUserPrestamista()
    {
        return $this->userPrestamista;
    }

    /**
     * Set factura
     *
     * @param string $factura
     * @return Prestamo
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;
    
        return $this;
    }

    /**
     * Get factura
     *
     * @return string 
     */
    public function getFactura()
    {
        return $this->factura;
    }

    
    /**
     * Set userDevolucion
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $userDevolucion
     * @return Prestamo
     */
    public function setUserDevolucion(\Administracion\UsuarioBundle\Entity\Perfil $userDevolucion)
    {
        $this->userDevolucion = $userDevolucion;
    
        return $this;
    }

    /**
     * Get userDevolucion
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil
     */
    public function getUserDevolucion()
    {
        return $this->userDevolucion;
    }
    
    
}
