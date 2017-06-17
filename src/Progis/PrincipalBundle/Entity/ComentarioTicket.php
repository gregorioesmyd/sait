<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.comentarioticket")
 * @ORM\Entity
 */
class ComentarioTicket
{
    
    /**
     * @var \Frontend\ProyectoBundle\Entity\Ticket
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Progis\TicketBundle\Entity\Ticket")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     * })
     */
    private $ticket;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Comentarioarchivo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comentarioarchivo_id", referencedColumnName="id")
     * })
     */
    private $comentarioarchivo;
    

     

    /**
     * Set ticket
     *
     * @param \Progis\TicketBundle\Entity\Ticket $ticket
     * @return ComentarioTicket
     */
    public function setTicket(\Progis\TicketBundle\Entity\Ticket $ticket)
    {
        $this->ticket = $ticket;
    
        return $this;
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
     * Set comentarioarchivo
     *
     * @param \Progis\PrincipalBundle\Entity\Comentarioarchivo $comentarioarchivo
     * @return ComentarioTicket
     */
    public function setComentarioarchivo(\Progis\PrincipalBundle\Entity\Comentarioarchivo $comentarioarchivo)
    {
        $this->comentarioarchivo = $comentarioarchivo;
    
        return $this;
    }

    /**
     * Get comentarioarchivo
     *
     * @return \Progis\PrincipalBundle\Entity\Comentarioarchivo 
     */
    public function getComentarioarchivo()
    {
        return $this->comentarioarchivo;
    }
}