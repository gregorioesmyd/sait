<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CintasVirgenes
 *
 * @ORM\Table(name="videoteca.cintavigen")
 * @ORM\Entity
 */
class CintasVirgenes
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
     * @ORM\Column(name="nombre_cv", type="string", length=255)
     *@Assert\NotNull(message="Debe de indicar el nombre de la cinta")
     */
    private $nombreCv;

    /**
     * @var integer
     *
     * @ORM\Column(name="existencia", type="integer")
     *@Assert\NotNull(message="Debe de cantidad existente de la cinta a registrar")
     */
    private $existencia;

    /**
     * @var \Formato.idFormato
     *
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Formato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_formato", referencedColumnName="id")
     * })
     * 
     *@Assert\NotNull(message="Debe seleccionar un tipo de formato")
     * 
     */
    private $idFormato;


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
     * Set nombreCv
     *
     * @param string $nombreCv
     * @return CintasVirgenes
     */
    public function setNombreCv($nombreCv)
    {
        $this->nombreCv = $nombreCv;
    
        return $this;
    }

    /**
     * Get nombreCv
     *
     * @return string 
     */
    public function getNombreCv()
    {
        return $this->nombreCv;
    }

    /**
     * Set existencia
     *
     * @param integer $existencia
     * @return CintasVirgenes
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;
    
        return $this;
    }

    /**
     * Get existencia
     *
     * @return integer 
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Set idFormato
     *
     * @param \Frontend\VideotecaBundle\Entity\CintasVirgenes $idFormato
     * @return CintasVirgenes
     */
    public function setIdFormato(\Frontend\VideotecaBundle\Entity\Formato $idFormato = null)
    {
        $this->idFormato = $idFormato;
    
        return $this;
    }

    /**
     * Get idFormato
     *
     * @return Frontend\VideotecaBundle\Entity\CintasVirgenes  
     */
    public function getIdFormato()
    {
        return $this->idFormato;
    }

    public function __toString() {
        
        return $this->nombreCv;
    }
}