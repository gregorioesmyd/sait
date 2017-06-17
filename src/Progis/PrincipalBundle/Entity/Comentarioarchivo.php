<?php

namespace Progis\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Documento
 *
 * @ORM\Table(name="progis.comentarioarchivo")
 * @ORM\Entity
 */
class Comentarioarchivo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="progis.comentarioarchivo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="text", nullable=false)
     * @Assert\NotBlank(message="Debe escribir un comentario.").
     */
    private $comentario;


    
    /**
     * @Assert\File(maxSize="5000000", maxSizeMessage="El límite de tamaño de archivo es de 5M.")
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
     * 1 normal, 2 por dependencia, 3 por revisión, por solucion
     * @ORM\Column(name="tipo", type="integer", nullable=false)
     * @Assert\NotBlank(message="Debe seleccionar un tipo de comentario.").
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="fechahora", type="datetime", nullable=false)
     */
    private $fechahora;

    
     /**
     * @var \Frontend\ProyectoBundle\Entity\Actividad
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * })
     */
    private $responsable;

    

    public function getId()
    {
        return $this->id;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    
        return $this;
    }

    public function getComentario()
    {
        return $this->comentario;
    }
    
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    public function getTipo()
    {
        return $this->tipo;
    }
    


    public function setFile($file = null)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    
        return $this;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }
    
    public function setIdmodulo($idmodulo)
    {
        $this->idmodulo = $idmodulo;
    
        return $this;
    }
 
    public function setFechahora($fechahora)
    {
        $this->fechahora = $fechahora;

        return $this;
    }

    /**
     * Get fechahora
     *
     * @return string 
     */
    public function getFechahora()
    {
        return $this->fechahora;
    }

    /**
     * Set responsableId
     *
     * @param integer $responsableId
     * @return Tarea
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsableId
     *
     * @return integer 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }


    public function __toString()
    {
        return $this->getComentario();
    }
}