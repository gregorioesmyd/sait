<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Formato
 *
 * @ORM\Table(name="videoteca.formato")
 * @ORM\Entity
 */
class Formato
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_formato", type="string", length=255)
     */
    private $descripcionFormato;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\VideotecaBundle\Entity\UltimoCodigo", mappedBy="formato")
     **/
    private $ultimoCodigos;
    

    public function __construct() {
        $this->ultimoCodigos = new ArrayCollection();
    }

    /**
     *
     */
    public function __toString()
    {
        return $this->descripcionFormato;
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
     * Set descripcionFormato
     *
     * @param string $descripcionFormato
     * @return Formato
     */
    public function setDescripcionFormato($descripcionFormato)
    {
        $this->descripcionFormato = $descripcionFormato;
    
        return $this;
    }

    /**
     * Get descripcionFormato
     *
     * @return string 
     */
    public function getDescripcionFormato()
    {
        return $this->descripcionFormato;
    }

    /**
     * Add ultimoCodigos
     *
     * @param \Frontend\VideotecaBundle\Entity\UltimoCodigo $ultimoCodigos
     * @return Formato
     */
    public function addUltimoCodigo(\Frontend\VideotecaBundle\Entity\UltimoCodigo $ultimoCodigos)
    {
        $this->ultimoCodigos[] = $ultimoCodigos;
    
        return $this;
    }

    /**
     * Remove ultimoCodigos
     *
     * @param \Frontend\VideotecaBundle\Entity\UltimoCodigo $ultimoCodigos
     */
    public function removeUltimoCodigo(\Frontend\VideotecaBundle\Entity\UltimoCodigo $ultimoCodigos)
    {
        $this->ultimoCodigos->removeElement($ultimoCodigos);
    }

    /**
     * Get ultimoCodigos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUltimoCodigos()
    {
        return $this->ultimoCodigos;
    }
}