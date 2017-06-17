<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Documento
 *
 * @ORM\Table(name="progis.trabajoticket")
 * @ORM\Entity
 */


class TrabajoTicket extends TrabajoRealizado
{


    /**
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\TicketBundle\Entity\ProcesarTicket")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ticket_id", referencedColumnName="ticket_id")
     * })
     */
    private $ticket;

  



    /**
     * Set ticket
     *
     * @param \Progis\TicketBundle\Entity\ProcesarTicket $ticket
     * @return TrabajoTicket
     */
    public function setTicket(\Progis\TicketBundle\Entity\ProcesarTicket $ticket = null)
    {
        $this->ticket = $ticket;
    
        return $this;
    }

    /**
     * Get ticket
     *
     * @return \Progis\TicketBundle\Entity\ProcesarTicket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }
    

}