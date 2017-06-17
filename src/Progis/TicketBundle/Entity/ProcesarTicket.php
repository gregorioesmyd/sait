<?php

namespace Progis\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Procesar
 *
 * @ORM\Table(name="progis.procesarticket")
 * @ORM\Entity
 */
class ProcesarTicket
{
    
    /**
     * @var string
     
     * @ORM\Column(name="tiempoestimado", type="integer", nullable=false)
     * @Assert\NotBlank(message="El tiempo estimado no deben estar en blanco.")
     * @Assert\Type(type="digit", message="El tiempo debe ser numérico").
     * @Assert\NotEqualTo(value="0", message="El tiempo no puede ser igual a 0.")
     */
    private $tiempoestimado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comienzo", type="datetime", nullable=true)
     */
    private $comienzo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fin", type="datetime", nullable=true)
     */
    private $fin;
    
    /**
     * @var string
     *
     * @ORM\Column(name="retardo", type="boolean", nullable=true)
     */
    private $retardo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="correoretardo", type="boolean", nullable=false)
     */
    private $correoretardo;

   
    /**
     * @var string
     *
     * @ORM\Column(name="tiemporeal", type="string", nullable=true)
     */
    private $tiemporeal;
    
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
     * @ORM\Column(name="ubicacion", type="integer", nullable=false)
     */
    private $ubicacion;
    
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
     * @var \Etapa
     *
     * @ORM\ManyToOne(targetEntity="Progis\PrincipalBundle\Entity\Miembroespacio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un usuario.")
     */
    private $responsable;
    
    /**
     * @var \Subcategoria
     *
     * @ORM\ManyToOne(targetEntity="Subcategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subcategoria_id", referencedColumnName="id")
     * })
     */
    private $subcategoria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    //1 dias, 2 horas y 3 minutos
    private $orden;
    
    /**
     * @var string
     *
     * @ORM\Column(name="justificacion", type="boolean", nullable=true)
     */
    private $justificacion;
    
    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registradopor_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $registradopor;
    
    /**
     * Set orden
     *
     * @param integer $orden
     * @return Procesar
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }
    
    /**
     * Set ubicacion
     *
     * @param integer $ubicacion
     * @return Procesar
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return integer 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }
    
    public function setJustificacion($justificacion)
    {
        $this->justificacion = $justificacion;
    
        return $this;
    }

    public function getJustificacion()
    {
        return $this->justificacion;
    }

    /**
     * Set subcategoria
     *
     * @param \Progis\TicketBundle\Entity\Subcategoria $subcategoria
     * @return Ticket
     */
    public function setSubcategoria(\Progis\TicketBundle\Entity\Subcategoria $subcategoria = null)
    {
        $this->subcategoria = $subcategoria;
    
        return $this;
    }

    /**
     * Get subcategoria
     *
     * @return \Progis\TicketBundle\Entity\Subcategoria 
     */
    public function getSubcategoria()
    {
        return $this->subcategoria;
    }

    
    /**
     * Set comienzo
     *
     * @param integer $comienzo
     * @return Procesar
     */
    public function setComienzo($comienzo)
    {
        $this->comienzo = $comienzo;

        return $this;
    }

    /**
     * Get comienzo
     *
     * @return integer 
     */
    public function getComienzo()
    {
        return $this->comienzo;
    }
    
    /**
     * Set retardo
     *
     * @param integer $retardo
     * @return Procesar
     */
    public function setRetardo($retardo)
    {
        $this->retardo = $retardo;

        return $this;
    }

    /**
     * Get retardo
     *
     * @return integer 
     */
    public function getRetardo()
    {
        return $this->retardo;
    }
    
    /**
     * Set correoretardo
     *
     * @param integer $correoretardo
     * @return Procesar
     */
    public function setCorreoretardo($correoretardo)
    {
        $this->correoretardo = $correoretardo;

        return $this;
    }

    /**
     * Get correoretardo
     *
     * @return integer 
     */
    public function getCorreoretardo()
    {
        return $this->correoretardo;
    }
    
    /**
     * Set fin
     *
     * @param integer $fin
     * @return Procesar
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return integer 
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set tiempoestimado
     *
     * @param integer $tiempoestimado
     * @return Procesar
     */
    public function setTiempoestimado($tiempoestimado)
    {
        $this->tiempoestimado = $tiempoestimado;

        return $this;
    }

    /**
     * Get tiempoestimado
     *
     * @return integer 
     */
    public function getTiempoestimado()
    {
        return $this->tiempoestimado;
    }
    
    /**
     * Set tiemporeal
     *
     * @param integer $tiemporeal
     * @return Procesar
     */
    public function setTiemporeal($tiemporeal)
    {
        $this->tiemporeal = $tiemporeal;

        return $this;
    }

    /**
     * Get tiemporeal
     *
     * @return integer 
     */
    public function getTiemporeal()
    {
        return $this->tiemporeal;
    }
    
    /**
     * Set tipotiempo
     *
     * @param integer $tipotiempo
     * @return Procesar
     */
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
     * Get responsable
     *
     * @return \Frontend\ProyectoBundle\Entity\Responsable 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
    
    /**
     * Set responsable
     *
     * @param \Frontend\ProyectoBundle\Entity\Responsable $responsable
     * @return Procesar
     */
    public function setResponsable(\Progis\PrincipalBundle\Entity\Miembroespacio $responsable = null)
    {
        $this->responsable = $responsable;

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
     * Set ticket
     *
     * @param \Progis\TicketBundle\Entity\Ticket $ticket
     * @return Procesar
     */
    public function setTicket(\Progis\TicketBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }
    
    /**
     * Set registradopor
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $registradopor
     * @return Operador
     */
    public function setRegistradopor(\Administracion\UsuarioBundle\Entity\Perfil $registradopor = null)
    {
        $this->registradopor = $registradopor;
    
        return $this;
    }

    /**
     * Get registradopor
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getRegistradopor()
    {
        return $this->registradopor;
    }

}
