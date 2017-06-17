<?php

namespace Frontend\EstudioCabinaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estudiocabina.estatus
 *
 * @ORM\Table(name="estudiocabina.estatus")
 * @ORM\Entity
 */
class Estatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="estudiocabina.estatus_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="estatus", type="string", nullable=false)
     */
    private $estatus;



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
     * Set estatus
     *
     * @param string $estatus
     * @return Estatus
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    
        return $this;
    }

    /**
     * Get estatus
     *
     * @return string 
     */
    public function getEstatus()
    {
        return $this->estatus;
    }
}