<?php

namespace Frontend\EncuestaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pregunta
 *
 * @ORM\Table(name="encuesta.pregunta")
 * @ORM\Entity
 */

class Pregunta
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.pregunta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Encuesta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Encuesta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idencuesta", referencedColumnName="id", nullable=false)
     * })
     */
    private $idencuesta;

    /**
     * @var \Tipopregunta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Tipopregunta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipopregunta", referencedColumnName="id", nullable=false)
     * })
     */
    private $tipopregunta;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempopregunta", type="integer", nullable=true)
     */
    private $tiempopregunta;


    /**
     * @var string
     *
     * @ORM\Column(name="pregunta", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Coloque una Pregunta.")
     */
    private $pregunta;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idencuesta
     *
     * @param integer $idencuesta
     * @return Pregunta
     */
    public function setIdencuesta($idencuesta)
    {
        $this->idencuesta = $idencuesta;

        return $this;
    }

    /**
     * Get idencuesta
     *
     * @return integer
     */
    public function getIdencuesta()
    {
        return $this->idencuesta;
    }


    /**
     * Set tipopregunta
     *
     * @param integer $tipopregunta
     * @return Pregunta
     */
    public function setTipopregunta($tipopregunta)
    {
        $this->tipopregunta = $tipopregunta;

        return $this;
    }

    /**
     * Get tipopregunta
     *
     * @return integer
     */
    public function getTipopregunta()
    {
        return $this->tipopregunta;
    }

    /**
     * Set pregunta
     *
     * @param string $pregunta
     * @return Pregunta
     */
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return string
     */
    public function getPregunta()
    {
        return $this->pregunta;
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


    /**
     * Set tiempopregunta
     *
     * @param integer $tiempopregunta
     * @return Pregunta
     */
    public function setTiempopregunta($tiempopregunta)
    {
        $this->tiempopregunta = $tiempopregunta;

        return $this;
    }

    /**
     * Get tiempopregunta
     *
     * @return integer
     */
    public function getTiempopregunta()
    {
        return $this->tiempopregunta;
    }



    public function __toString()
    {
        return $this->getPregunta();
    }
}
