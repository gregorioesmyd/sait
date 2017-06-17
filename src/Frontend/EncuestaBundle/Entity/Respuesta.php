<?php

namespace Frontend\EncuestaBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Respuesta
 *
 * @ORM\Table(name="encuesta.respuesta")
 * @ORM\Entity
 */
class Respuesta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.respuesta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Idpregunta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Pregunta")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idpregunta", referencedColumnName="id", nullable=false)
     * })
     */
    private $idpregunta;

    /**
     * @var string
     *
     * @ORM\Column(name="respuesta", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Coloque una respuesta.")
     */
    private $respuesta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="correcta", type="boolean", nullable=true)
     */
    private $correcta;




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
     * Set idpregunta
     *
     * @param \Frontend\EncuestaBundle\Entity\Pregunta $idpregunta
     * @return Idpregunta
     */
    public function setIdpregunta(\Frontend\EncuestaBundle\Entity\Pregunta $idpregunta = null)
    {
        $this->idpregunta = $idpregunta;

        return $this;
    }

    /**
     * Get idpregunta
     *
     * @return \Frontend\EncuestaBundle\Entity\Pregunta
     */
    public function getIdpregunta()
    {
        return $this->idpregunta;
    }

    /**
     * Set respuesta
     *
     * @param string $respuesta
     * @return Respuesta
     */
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    /**
     * Get respuesta
     *
     * @return string
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    /**
     * Set correcta
     *
     * @param boolean $correcta
     * @return User
     */
    public function setCorrecta($correcta)
    {
        $this->correcta = $correcta;

        return $this;
    }

    /**
     * Get correcta
     *
     * @return boolean
     */
    public function getCorrecta()
    {
        return $this->correcta;
    }



    public function __toString()
    {
        return $this->getRespuesta();
    }

}
