<?php

namespace Progis\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Ticket
 * 
 * @ORM\Table(name="progis.ticket")
 * @ORM\Entity
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.ticket_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;


    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante_id", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank(message="Debe seleccionar un solicitante.")
     */
    private $solicitante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechasolicitud", type="date", nullable=false)
     */
    private $fechasolicitud;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horasolicitud", type="time", nullable=false)
     */
    private $horasolicitud;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechasolucion", type="date", nullable=true)
     */
    private $fechasolucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horasolucion", type="time", nullable=true)
     */
    private $horasolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud", type="string", length=1000, nullable=false)
     * @Assert\NotBlank(message="Debe escribir la solicitud.")
     * @Assert\Length(
     *      min = "10",
     *      max = "1000",
     *      minMessage = "La solicitud no puede se menor a {{ limit }} caracteres de largo.",
     *      maxMessage = "La solicitud no puede se mayor a {{ limit }} caracteres de largo."
     * )
     * )
     */
    private $solicitud;

    /**
     * @var string
     *
     * @ORM\Column(name="solucion", type="text", nullable=true)
     */
    private $solucion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reasignado", type="boolean", nullable=false)
     */
    private $reasignado=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus=1;

    /**
     * @var \Nivelorganizacional
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Nivelorganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nivelorganizacional_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar una unidad.")
     */
    private $nivelorganizacional;
    /**
     * @var string
     *
     * @Assert\NotBlank(message="La extensión no debe estar en blanco.")
     * @Assert\Type(type="digit", message="La extensión debe ser numérica").
     * @Assert\NotEqualTo(value="0", message="La extensión no puede ser igual a 0.")
     */
    private $extension;
    
     /**
     * @Assert\File(
     * maxSize="5M", 
     * maxSizeMessage="El archivo que intenta subir debe ser menos 5Mb") 
     *  
     */
    private $file;
    

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=500, nullable=true)
     */
    private $archivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="valoracion", type="integer", nullable=true)
     */
    private $valoracion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comentario_valoracion", type="text", nullable=true)
     */
    private $comentarioValoracion;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Operador
     */
    public function setSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $solicitante = null)
    {
        $this->solicitante = $solicitante;
    
        return $this;
    }

    /**
     * Get solicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getSolicitante()
    {
        return $this->solicitante;
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

    /**
     * Set fechasolicitud
     *
     * @param \DateTime $fechasolicitud
     * @return Ticket
     */
    public function setFechasolicitud($fechasolicitud)
    {
        $this->fechasolicitud = $fechasolicitud;
    
        return $this;
    }

    /**
     * Get fechasolicitud
     *
     * @return \DateTime 
     */
    public function getFechasolicitud()
    {
        return $this->fechasolicitud;
    }
    
    /**
     * Set extension
     *
     * @param \DateTime $extension
     * @return Ticket
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * Get extension
     *
     * @return \DateTime 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set horasolicitud
     *
     * @param \DateTime $horasolicitud
     * @return Ticket
     */
    public function setHorasolicitud($horasolicitud)
    {
        $this->horasolicitud = $horasolicitud;
    
        return $this;
    }

    /**
     * Get horasolicitud
     *
     * @return \DateTime 
     */
    public function getHorasolicitud()
    {
        return $this->horasolicitud;
    }

    /**
     * Set fechasolucion
     *
     * @param \DateTime $fechasolucion
     * @return Ticket
     */
    public function setFechasolucion($fechasolucion)
    {
        $this->fechasolucion = $fechasolucion;
    
        return $this;
    }

    /**
     * Get fechasolucion
     *
     * @return \DateTime 
     */
    public function getFechasolucion()
    {
        return $this->fechasolucion;
    }

    /**
     * Set horasolucion
     *
     * @param \DateTime $horasolucion
     * @return Ticket
     */
    public function setHorasolucion($horasolucion)
    {
        $this->horasolucion = $horasolucion;
    
        return $this;
    }

    /**
     * Get horasolucion
     *
     * @return \DateTime 
     */
    public function getHorasolucion()
    {
        return $this->horasolucion;
    }

    /**
     * Set solicitud
     *
     * @param string $solicitud
     * @return Ticket
     */
    public function setSolicitud($solicitud)
    {
        $this->solicitud = $solicitud;
    
        return $this;
    }

    /**
     * Get solicitud
     *
     * @return string 
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set solucion
     *
     * @param string $solucion
     * @return Ticket
     */
    public function setSolucion($solucion)
    {
        $this->solucion = $solucion;
    
        return $this;
    }

    /**
     * Get solucion
     *
     * @return string 
     */
    public function getSolucion()
    {
        return $this->solucion;
    }

    /**
     * Set reasignado
     *
     * @param boolean $reasignado
     * @return Ticket
     */
    public function setReasignado($reasignado)
    {
        $this->reasignado = $reasignado;
    
        return $this;
    }

    /**
     * Get reasignado
     *
     * @return boolean 
     */
    public function getReasignado()
    {
        return $this->reasignado;
    }

    /**
     * Set estatus
     *
     * @param string $estatus
     * @return Ticket
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return string 
     */
    public function getEstatus()
    {
        return $this->estatus;
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
     * Set archivo
     *
     * @param string $archivo
     * @return Ticket
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    
        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set valoracion
     *
     * @param integer $valoracion
     * @return Ticket
     */
    public function setValoracion($valoracion)
    {
        $this->valoracion = $valoracion;
    
        return $this;
    }

    /**
     * Get valoracion
     *
     * @return integer 
     */
    public function getValoracion()
    {
        return $this->valoracion;
    }

    /**
     * Set comentarioValoracion
     *
     * @param string $comentarioValoracion
     * @return Ticket
     */
    public function setComentarioValoracion($comentarioValoracion)
    {
        $this->comentarioValoracion = $comentarioValoracion;
    
        return $this;
    }

    /**
     * Get comentarioValoracion
     *
     * @return string 
     */
    public function getComentarioValoracion()
    {
        return $this->comentarioValoracion;
    }
}