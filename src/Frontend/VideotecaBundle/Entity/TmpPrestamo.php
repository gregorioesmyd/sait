<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TmpPrestamo
 *
 * @ORM\Table(name="videoteca.tmpPrestamo")
 * @ORM\Entity
 */
class TmpPrestamo
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
     *@ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta")
     *@ORM\JoinColumn(name="codigo", referencedColumnName="id")
     */
    private $codigo;
    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="prestamista", referencedColumnName="id", nullable=false)
     * })
     */
    private $prestamista;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

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
     * Set codigo
     *
     * @param \Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta $codigo
     * @return TmpPrestamo
     */
    
    public function setCodigo(\Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta $codigo = null)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     *@return \Frontend\VideotecaBundle\Entity\WrapperCinta\Cinta  
     */
    public function getCodigo()
    {
        return $this->codigo;
    }
    
    /**
     * Set prestamista
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $prestamista
     * @return TmpPrestamo
     */
    public function setPrestamista(\Administracion\UsuarioBundle\Entity\Perfil $prestamista)
    {
        $this->prestamista = $prestamista;
    
        return $this;
    }

    /**
     * Get prestamista
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getPrestamista()
    {
        return $this->prestamista;
    }
    
      /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return TmpPrestamo
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;
    
        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime 
     */
    public function getCreado()
    {
        return $this->creado;
    }
}
