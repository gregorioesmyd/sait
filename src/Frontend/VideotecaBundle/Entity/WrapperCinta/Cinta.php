<?php

namespace Frontend\VideotecaBundle\Entity\WrapperCinta;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Cinta
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="videoteca.cinta")
 * @ORM\Entity()
 */
class Cinta
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
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     *
     */
    private $codigo;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "La Observacion debe ser al menos {{ limit }} caracteres"
     * )
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;


    /**
     * @var \Formato
     *
     * @Assert\NotBlank(message="El formato no puede estar vacio.")
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Formato")
     * @ORM\JoinColumn(name="formato_id", referencedColumnName="id")
     */
    private $formato;

    /**
     * @var \Duracion
     *
     * @Assert\NotBlank(message="La duracion no puede estar vacio.")
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Duracion")
     * @ORM\JoinColumn(name="duracion_id", referencedColumnName="id")
     */
    private $duracion;

    /**
     * @var \Evento
     *
     * @Assert\NotBlank(message="El evento no puede estar vacio.")
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Evento")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id")
     */
    private $evento;

    /**
     * @var \Marca
     *
     * @Assert\NotBlank(message="La marcas no puede estar vacio.")
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Marca")
     * @ORM\JoinColumn(name="marca_id", referencedColumnName="id")
     */
    private $marca;

    /**
     * @var \Perfil
     *
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $documentalista;

    /**
     * @var \TipoCinta
     *
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\TipoCinta")
     * @ORM\JoinColumn(name="tipocinta_id", referencedColumnName="id")
     */
    protected $tipoCinta;

    /**
     * @var \Status
     *
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

    /**
     * @var \Categoria
     *
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    protected $categoria;

    /**
     * @var \Propiedad
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Propiedad")
     * @0RM\JoinColumn(name="propiedad_id", referencedColumnName="id")
     */
    private $propiedad;

    /**
     * @ORM\OneToMany(targetEntity="Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento", mappedBy="cinta")
     **/
    private $segmentos;

    /**
     *
     */
    public function __construct() {
        $this->segmentos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->codigo;
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
     * Set codigo
     *
     * @param string $codigo
     * @return Cinta
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Cinta
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Cinta
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;
    
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreadoValue()
    {
        $this->creado = new \DateTime();
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

    /**
     * Set formato
     *
     * @param \Frontend\VideotecaBundle\Entity\Formato $formato
     * @return Cinta
     */
    public function setFormato(\Frontend\VideotecaBundle\Entity\Formato $formato = null)
    {
        $this->formato = $formato;
    
        return $this;
    }

    /**
     * Get formato
     *
     * @return \Frontend\VideotecaBundle\Entity\Formato 
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Set duracion
     *
     * @param \Frontend\VideotecaBundle\Entity\Duracion $duracion
     * @return Cinta
     */
    public function setDuracion(\Frontend\VideotecaBundle\Entity\Duracion $duracion = null)
    {
        $this->duracion = $duracion;
    
        return $this;
    }

    /**
     * Get duracion
     *
     * @return \Frontend\VideotecaBundle\Entity\Duracion 
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set evento
     *
     * @param \Frontend\VideotecaBundle\Entity\Evento $evento
     * @return Cinta
     */
    public function setEvento(\Frontend\VideotecaBundle\Entity\Evento $evento = null)
    {
        $this->evento = $evento;
    
        return $this;
    }

    /**
     * Get evento
     *
     * @return \Frontend\VideotecaBundle\Entity\Evento 
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Set marca
     *
     * @param \Frontend\VideotecaBundle\Entity\Marca $marca
     * @return Cinta
     */
    public function setMarca(\Frontend\VideotecaBundle\Entity\Marca $marca = null)
    {
        $this->marca = $marca;
    
        return $this;
    }

    /**
     * Get marca
     *
     * @return \Frontend\VideotecaBundle\Entity\Marca 
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set documentalista
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $documentalista
     * @return Cinta
     */
    public function setDocumentalista(\Administracion\UsuarioBundle\Entity\Perfil $documentalista)
    {
        $this->documentalista = $documentalista;
    
        return $this;
    }

    /**
     * Get documentalista
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getDocumentalista()
    {
        return $this->documentalista;
    }

    /**
     * Set tipoCinta
     *
     * @param \Frontend\VideotecaBundle\Entity\TipoCinta $tipoCinta
     * @return Cinta
     */
    public function setTipoCinta(\Frontend\VideotecaBundle\Entity\TipoCinta $tipoCinta = null)
    {
        $this->tipoCinta = $tipoCinta;
    
        return $this;
    }

    /**
     * Get tipoCinta
     *
     * @return \Frontend\VideotecaBundle\Entity\TipoCinta 
     */
    public function getTipoCinta()
    {
        return $this->tipoCinta;
    }

    /**
     * Set status
     *
     * @param \Frontend\VideotecaBundle\Entity\Status $status
     * @return Cinta
     */
    public function setStatus(\Frontend\VideotecaBundle\Entity\Status $status = null)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \Frontend\VideotecaBundle\Entity\Status 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set categoria
     *
     * @param \Frontend\VideotecaBundle\Entity\Categoria $categoria
     * @return Cinta
     */
    public function setCategoria(\Frontend\VideotecaBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Frontend\VideotecaBundle\Entity\Categoria 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set propiedad
     *
     * @param \Frontend\VideotecaBundle\Entity\Propiedad $propiedad
     * @return Cinta
     */
    public function setPropiedad(\Frontend\VideotecaBundle\Entity\Propiedad $propiedad = null)
    {
        $this->propiedad = $propiedad;
    
        return $this;
    }

    /**
     * Get propiedad
     *
     * @return \Frontend\VideotecaBundle\Entity\Propiedad 
     */
    public function getPropiedad()
    {
        return $this->propiedad;
    }

    /**
     * Add segmentos
     *
     * @param \Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento $segmentos
     * @return Cinta
     */
    public function addSegmento(\Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento $segmentos)
    {
        $this->segmentos[] = $segmentos;
    
        return $this;
    }

    /**
     * Remove segmentos
     *
     * @param \Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento $segmentos
     */
    public function removeSegmento(\Frontend\VideotecaBundle\Entity\WrapperSegmento\Segmento $segmentos)
    {
        $this->segmentos->removeElement($segmentos);
    }

    /**
     * Get segmentos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSegmentos()
    {
        return $this->segmentos;
    }
}