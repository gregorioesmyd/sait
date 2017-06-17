<?php

namespace Frontend\EncuestaBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votos
 *
 * @ORM\Table(name="encuesta.resultados")
 * @ORM\Entity
 */
class Resultados
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.resultados_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idusuario", referencedColumnName="id", nullable=false)
     * })
     */
    private $idusuario;

    /**
     * @var float
     *
     * @ORM\Column(name="puntos", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $puntos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;



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
     * Set idusuario
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $idusuario
     * @return Perfil
     */
    public function setIdusuario(\Administracion\UsuarioBundle\Entity\Perfil $idusuario = null)
    {
        $this->idusuario = $idusuario;

        return $this;
    }

    /**
     * Get idusuario
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil
     */
    public function getIdusuario()
    {
        return $this->idusuario;
    }

    /**
     * Set puntos
     *
     * @param float $puntos
     * @return Encuesta
     */
    public function setPuntos($puntos)
    {
        $this->puntos = $puntos;

        return $this;
    }

    /**
     * Get puntos
     *
     * @return float
     */
    public function getPuntos()
    {
        return $this->puntos;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Resultados
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }
}
