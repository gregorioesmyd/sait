<?php

namespace Frontend\EncuestaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Encuesta
 *
 * @ORM\Table(name="encuesta.tipoencuesta")
 * @ORM\Entity
 */
class Tipoencuesta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.tipoencuesta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoencuesta", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Coloque un tipo de encuesta de encuesta.")
     */
    private $tipoencuesta;

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
     * Set tipoencuesta
     *
     * @param string $tipoencuesta
     * @return Encuesta
     */
    public function setTipoencuesta($tipoencuesta)
    {
        $this->tipoencuesta = $tipoencuesta;

        return $this;
    }

    /**
     * Get tipoencuesta
     *
     * @return string
     */
    public function getTipoencuesta()
    {
        return $this->tipoencuesta;
    }

    public function __toString()
    {
        return $this->getTipoencuesta();
    }
}
