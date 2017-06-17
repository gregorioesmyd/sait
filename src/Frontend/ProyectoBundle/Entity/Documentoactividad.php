<?php

namespace Frontend\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Documento
 *
 * @ORM\Table(name="proyecto.documentoactividad")
 * @ORM\Entity
 */
class Documentoactividad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="proyecto.documentoactividad_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  

    /**
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $actividadId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", nullable=false)
     * @Assert\NotBlank(message="El campo Descripción de Documento no puede estar en blanco.").
     */
    private $descripcion;


    /**
     * @Assert\File(maxSize="5000000", maxSizeMessage="El archivo que intenta subir es demasiado grande.")
     * @Assert\NotBlank(message="Debe adjuntar un Archivo").
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /*
     * Set actividadId
     *
     * @param \Frontend\ProyectoBundle\Entity\Actividad $actividadId
     * @return Comentario
     */
    public function setActividadId(\Frontend\ProyectoBundle\Entity\Actividad $actividadId = null)
    {
        $this->actividadId = $actividadId;
    
        return $this;
    }

    /**
     * Get actividadId
     *
     * @return \Frontend\ProyectoBundle\Entity\Actividad 
     */
    public function getActividadId()
    {
        return $this->actividadId;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Documento
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile($file = null)
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

    public function __toString()
    {
        return $this->getDescripcion();
    }
}