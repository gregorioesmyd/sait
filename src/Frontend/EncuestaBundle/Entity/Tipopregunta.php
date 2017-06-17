<?php

namespace Frontend\EncuestaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * tipopregunta
 *
 * @ORM\Table(name="encuesta.tipopregunta")
 * @ORM\Entity
 */
class Tipopregunta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encuesta.tipopregunta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tipopregunta", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Coloque un tipo de encuesta de encuesta.")
     */
    private $tipopregunta;

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
     * Set tipopregunta
     *
     * @param string $tipopregunta
     * @return tipopregunta
     */
    public function setTipopregunta($tipopregunta)
    {
        $this->tipopregunta = $tipopregunta;

        return $this;
    }

    /**
     * Get tipopregunta
     *
     * @return string
     */
    public function getTipopregunta()
    {
        return $this->tipopregunta;
    }

    public function __toString()
    {
        return $this->getTipopregunta();
    }
}
