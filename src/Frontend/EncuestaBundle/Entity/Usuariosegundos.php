<?php

namespace Frontend\EncuestaBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuariosegundos
 *
 * @ORM\Table(name="encuesta.usuriosegundos")
 * @ORM\Entity
 */
class Usuariosegundos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.usuriosegundos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var \Idpregunta
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EncuestaBundle\Entity\Encuesta")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idencuesta", referencedColumnName="id", nullable=false)
     * })
     */
   private $idencuesta;


    /**
     * @var integer
     *
     * @ORM\Column(name="segundos", type="integer", nullable=true)
     */
    private $segundos;


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
     * Set idencuesta
     *
     * @param \Frontend\EncuestaBundle\Entity\Encuesta $idencuesta
     * @return Encuesta
     */
    public function setIdencuesta(\Frontend\EncuestaBundle\Entity\Encuesta $idencuesta = null)
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
     * Set segundos
     *
     * @param integer $segundos
     * @return Pregunta
     */
    public function setSegundos($segundos)
    {
        $this->segundos = $segundos;

        return $this;
    }

    /**
     * Get segundos
     *
     * @return integer
     */
    public function getSegundos()
    {
        return $this->segundos;
    }


}
