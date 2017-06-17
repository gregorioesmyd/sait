<?php

namespace Frontend\EncuestaBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votos
 *
 * @ORM\Table(name="encuesta.votos")
 * @ORM\Entity
 */
class Votos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.votos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Idpregunta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Encuesta")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idencuesta", referencedColumnName="id", nullable=false)
     * })
     */
   private $idencuesta;


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
      * @var \Respuesta
      *
      * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Respuesta")
      * @ORM\JoinColumns({
      *  @ORM\JoinColumn(name="idrespuesta", referencedColumnName="id", nullable=false)
      * })
      */
    private $idrespuesta;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idusuario", referencedColumnName="id", nullable=false)
     * })
     */
    private $idusuario;

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
     * @param \Frontend\EncuestaBundle\Entity\Encuesta $idencuesta
     * @return Encuesta
     */
    public function setIdencuesta(\Frontend\EncuestaBundle\Entity\Encuesta $idencuesta)
    {
        $this->idencuesta = $idencuesta;

        return $this;
    }

    /**
     * Get idencuesta
     *
     * @return \Frontend\EncuestaBundle\Entity\Encuesta
     */
    public function getIdencuesta()
    {
        return $this->idencuesta;
    }





    /**
     * Set idpregunta
     *
     * @param \Frontend\EncuestaBundle\Entity\Pregunta $idpregunta
     * @return Pregunta
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
     * Set idrespuesta
     *
     * @param \Frontend\EncuestaBundle\Entity\Respuesta $idrespuesta
     * @return Respuesta
     */
    public function setIdrespuesta(\Frontend\EncuestaBundle\Entity\Respuesta $idrespuesta = null)
    {
        $this->idrespuesta = $idrespuesta;

        return $this;
    }

    /**
     * Get idrespuesta
     *
     * @return \Frontend\EncuestaBundle\Entity\Respuesta
     */
    public function getIdrespuesta()
    {
        return $this->idrespuesta;
    }


    /**
     * Set idusuario
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $idusuario
     * @return Perfil
     */
    public function setIdsuario(\Administracion\UsuarioBundle\Entity\Perfil $idusuario = null)
    {
        $this->idusuario = $idusuario;

        return $this;
    }

    /**
     * Get idusuario
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil
     */
    public function getIdsuario()
    {
        return $this->idusuario;
    }

    /**
     * Set segundos
     *
     * @param integer $segundos
     * @return Votos
     */
    public function setVotos($segundos)
    {
        $this->segundos = $segundos;

        return $this;
    }

    /**
     * Get segundos
     *
     * @return integer
     */
    public function getVotos()
    {
        return $this->segundos;
    }

}
