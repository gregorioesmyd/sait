<?php

namespace Frontend\ProyectoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comentario
 *
 * @ORM\Table(name="proyecto.etapa")
 * @ORM\Entity
 */
class Etapa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="proyecto.etapa_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;  


    /**
     * @var string
     *
     * @ORM\Column(name="etapa", type="string", nullable=false)
     * @Assert\NotBlank(message="La etapa no puede estar en blanco.").
     */
    private $etapa;

    public function getId()
    {
        return $this->id;
    }


    /**
     * Set etapa
     *
     * @param string $etapa
     * @return Cargo
     */
    public function setEtapa($etapa)
    {
        $this->etapa = $etapa;
    
        return $this;
    }

    /**
     * Get etapa
     *
     * @return string 
     */
    public function getEtapa()
    {
        return $this->etapa;
    }
    
    public function __toString() {
        return $this->etapa;
    }

   
}