<?php

namespace Frontend\EncuestaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Encuesta
 *
 * @ORM\Table(name="encuesta.encuesta")
 * @ORM\Entity
 */
class Encuesta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.encuesta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Coloque un nombre de encuesta.")
     */
    private $nombre;

    /**
     * @var \Tipoencuesta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Tipoencuesta")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="tipoencuesta", referencedColumnName="id", nullable=false)
     * })
     */
    private $tipoencuesta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicioencuesta", type="date", nullable=false)
     * @Assert\NotBlank(message="La fecha de inicio no puede estar en blanco.")
     */
    private $fechainicioencuesta;



    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horainicio", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora de inicio no puede estar en blanco.")
     */
    private $horainicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafinencuesta", type="date",nullable=false)
     * @Assert\NotBlank(message="La fecha de fin no puede estar en blanco.")
     */
    private $fechafinencuesta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horafin", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora de fin no puede estar en blanco.")
     */
    private $horafin;

    /**
     * @var string
     *
     * @ORM\Column(name="tema", type="string", length=1000, nullable=false)
     * @Assert\NotBlank(message="El tema de la encuesta no puede estar en blanco.")
     */
    private $tema;

    /**
     * @var integer
     *
     * @ORM\Column(name="numeropregunta", type="integer", nullable=false)
     * @Assert\NotBlank(message=" las preguntas a responder no puede estar en blanco.")
     */
    private $numeropregunta;

    /**
     * @var float
     *
     * @ORM\Column(name="puntospregunta", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $puntospregunta;


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
     * Set nombre
     *
     * @param string $nombre
     * @return Encuesta
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set tipoencuesta
     *
     * @param integer $tipoencuesta
     * @return Pregunta
     */
    public function setTipoencuesta($tipoencuesta)
    {
        $this->tipoencuesta = $tipoencuesta;

        return $this;
    }

    /**
     * Get tipoencuesta
     *
     * @return integer
     */
    public function getTipoencuesta()
    {
        return $this->tipoencuesta;
    }

    /**
     * Set fechainicioencuesta
     *
     * @param \DateTime $fechainicioencuesta
     * @return Encuesta
     */
    public function setFechainicioencuesta($fechainicioencuesta)
    {
        $this->fechainicioencuesta = $fechainicioencuesta;

        return $this;
    }

    /**
     * Get fechainicioencuesta
     *
     * @return \DateTime
     */
    public function getFechainicioencuesta()
    {
        return $this->fechainicioencuesta;
    }

    /**
     * Set horafin
     *
     * @param \DateTime  $horafin
     * @return Pautafijas
     */
    public function sethorafin($horafin)
    {
        $this->horafin = $horafin;

        return $this;
    }

    /**
     * Get horafin
     *
     * @return \DateTime
     */
    public function gethorafin()
    {
        return $this->horafin;
    }


    /**
     * Set fechafinencuesta
     *
     * @param \DateTime $fechafinencuesta
     * @return Encuesta
     */
    public function setFechafinencuesta($fechafinencuesta)
    {
        $this->fechafinencuesta = $fechafinencuesta;

        return $this;
    }

    /**
     * Get fechafinencuesta
     *
     * @return \DateTime
     */
    public function getFechafinencuesta()
    {
        return $this->fechafinencuesta;
    }


    /**
     * Set horainicio
     *
     * @param \DateTime $horainicio
     * @return Pautafijas
     */
    public function sethorainicio($horainicio)
    {
        $this->horainicio = $horainicio;

        return $this;
    }

    /**
     * Get horainicio
     *
     * @return \DateTime
     */
    public function gethorainicio()
    {
        return $this->horainicio;
    }

    /**
     * Set tema
     *
     * @param string $tema
     * @return Encuesta
     */
    public function setTema($tema)
    {
        $this->tema = $tema;

        return $this;
    }

    /**
     * Get tema
     *
     * @return string
     */
    public function getTema()
    {
        return $this->tema;
    }

    /**
     * Set numeropregunta
     *
     * @param integer $numeropregunta
     * @return Encuesta
     */
    public function setNumeropregunta($numeropregunta)
    {
        $this->numeropregunta = $numeropregunta;

        return $this;
    }

    /**
     * Get numeropregunta
     *
     * @return integer
     */
    public function getNumeropregunta()
    {
        return $this->numeropregunta;
    }


    /**
     * Set puntospregunta
     *
     * @param float $puntospregunta
     * @return Encuesta
     */
    public function setPuntospregunta($puntospregunta)
    {
        $this->puntospregunta = $puntospregunta;

        return $this;
    }

    /**
     * Get puntospregunta
     *
     * @return float
     */
    public function getPuntospregunta()
    {
        return $this->puntospregunta;
    }





    public function __toString()
    {
        return $this->getNombre();
    }
}
