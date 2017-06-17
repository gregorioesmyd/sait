<?php

namespace Progis\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subcategoria
 *
 * @ORM\Table(name="progis.subcategoria")
 * @ORM\Entity
 */
class Subcategoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.subcategoria_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subcategoria", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Debe escribir la subcategoria.")
     */
    private $subcategoria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tiempoatencion", type="integer",  nullable=true)
     * @Assert\NotBlank(message="El tiempo no debe estar en blanco.")
     * @Assert\Type(type="digit", message="El tiempo debe ser numérico").
     * @Assert\NotEqualTo(value="0", message="El tiempo no puede ser igual a 0.")
     */
    private $tiempoatencion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipotiempo", type="integer", nullable=true)
     */
    //1 dias, 2 horas y 3 minutos
    private $tipotiempo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="urlwiki", type="string", length=100, nullable=true)
     * @Assert\Url()
     */
    private $urlwiki;

    /**
     * @var \Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * })
     */
    private $categoria;

    /**
     * @var \complejidad
     *
     * @ORM\ManyToOne(targetEntity="Complejidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="complejidad_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar el nivel de complejidad.")
     */
    private $complejidad;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setTipotiempo($tipotiempo)
    {
        $this->tipotiempo = $tipotiempo;

        return $this;
    }

    /**
     * Get tipotiempo
     *
     * @return integer 
     */
    public function getTipotiempo()
    {
        return $this->tipotiempo;
    }
    public function setTiempoatencion($tiempoatencion)
    {
        $this->tiempoatencion = $tiempoatencion;

        return $this;
    }

    /**
     * Get tiempoatencion
     *
     * @return integer 
     */
    public function getTiempoatencion()
    {
        return $this->tiempoatencion;
    }
    
    public function setUrlwiki($urlwiki)
    {
        $this->urlwiki = $urlwiki;

        return $this;
    }

    /**
     * Get urlwiki
     *
     * @return integer 
     */
    public function getUrlwiki()
    {
        return $this->urlwiki;
    }
    
    /**
     * Set subcategoria
     *
     * @param string $subcategoria
     * @return Subcategoria
     */
    public function setSubcategoria($subcategoria)
    {
        $this->subcategoria = $subcategoria;
    
        return $this;
    }

    /**
     * Get subcategoria
     *
     * @return string 
     */
    public function getSubcategoria()
    {
        return $this->subcategoria;
    }

    /**
     * Set categoria
     *
     * @param \Progis\TicketBundle\Entity\Categoria $categoria
     * @return Subcategoria
     */
    public function setCategoria(\Progis\TicketBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Progis\TicketBundle\Entity\Categoria 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set complejidad
     *
     * @param \Progis\TicketBundle\Entity\Complejidad $complejidad
     * @return Subcategoria
     */
    public function setComplejidad(\Progis\TicketBundle\Entity\Complejidad $complejidad = null)
    {
        $this->complejidad = $complejidad;
    
        return $this;
    }

    /**
     * Get complejidad
     *
     * @return \Progis\TicketBundle\Entity\Complejidad 
     */
    public function getComplejidad()
    {
        return $this->complejidad;
    }
}