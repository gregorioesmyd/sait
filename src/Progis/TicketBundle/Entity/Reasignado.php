<?php

namespace Progis\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Unidad
 *
 * @ORM\Table(name="progis.reasignado")
 * @ORM\Entity
 */
class Reasignado
{
   
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.reasignado_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  
     /**
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     */
    private $responsable;
    
    /**
     * @var \Categoria
     * @ORM\ManyToOne(targetEntity="ticket")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     * })
     */
    private $ticket;

        /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id", nullable=false)
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
     * Set responsable
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsable
     * @return Miembroespacio
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;
    
        return $this;
    }

    /**
     * Get responsable
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
    

    
        /**
     * Get ticket
     *
     * @return \Progis\TicketBundle\Entity\Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set subticket
     *
     * @param \Progis\TicketBundle\Entity\Subticket $subticket
     * @return Ticket
     */
    public function setTicket(Ticket $ticket = null)
    {
        $this->ticket = $ticket;
    
        return $this;
    }
    

    /**
     * Set nivelorganizacional
     *
     * @param \Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional
     * @return Miembroespacio
     */
    public function setNivelorganizacional(\Administracion\UsuarioBundle\Entity\Nivelorganizacional $nivelorganizacional)
    {
        $this->nivelorganizacional = $nivelorganizacional;
    
        return $this;
    }

    /**
     * Get nivelorganizacional
     *
     * @return \Administracion\UsuarioBundle\Entity\Nivelorganizacional 
     */
    public function getNivelorganizacional()
    {
        return $this->nivelorganizacional;
    }

}

