<?php

namespace Progis\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categoria
 *
 * @ORM\Table(name="progis.categoria")
 * @ORM\Entity
 */
class Categoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.categoria_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="categoria", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Debe escribir la categoria.")
     */
    private $categoria;

    /**
     * @var \Nivelorganizacional
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id")
     * })
     */
    private $nivelorganizacional;


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
     * Set nivelorganizacional
     *
     * @param \Progis\TicketBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return Ticket
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional = null)
    {
        $this->nivelorganizacional = $nivelorganizacional;
    
        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Progis\TicketBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }

    /**
     * Set categoria
     *
     * @param string $categoria
     * @return Categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return string 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function __toString(){
        return $this->getCategoria();
    }
}