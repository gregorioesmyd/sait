<?php

namespace Frontend\VideotecaBundle\Entity\WrapperSegmento;

use Doctrine\ORM\Mapping as ORM;

/**
 * SgtSatelite
 *
 * @ORM\Table(name="videoteca.sgt_satelite")
 * @ORM\Entity
 */
class SgtSatelite extends Segmento
{

    const CLASE = "SgtSatelite";

    /**
     * @var string
     *
     * @ORM\Column(name="corresponsal", type="string", length=60)
     */
    private $corresponsal;

    /**
     * @var string
     *
     * @ORM\Column(name="nroREM", type="string", length=20)
     */
    private $nroREM;

    /**
     * @var integer
     *
     * @ORM\Column(name="nroHistoria", type="integer")
     */
    private $nroHistoria;

    /**
     * @var integer
     *
     * @ORM\Column(name="nroNota", type="integer")
     */
    private $nroNota;

    /**
     * Set corresponsal
     *
     * @param string $corresponsal
     * @return SgtSatelite
     */
    public function setCorresponsal($corresponsal)
    {
        $this->corresponsal = $corresponsal;
    
        return $this;
    }

    /**
     * Get corresponsal
     *
     * @return string 
     */
    public function getCorresponsal()
    {
        return $this->corresponsal;
    }

    /**
     * Set nroREM
     *
     * @param string $nroREM
     * @return SgtSatelite
     */
    public function setNroREM($nroREM)
    {
        $this->nroREM = $nroREM;
    
        return $this;
    }

    /**
     * Get nroREM
     *
     * @return string 
     */
    public function getNroREM()
    {
        return $this->nroREM;
    }

    /**
     * Set nroHistoria
     *
     * @param integer $nroHistoria
     * @return SgtSatelite
     */
    public function setNroHistoria($nroHistoria)
    {
        $this->nroHistoria = $nroHistoria;
    
        return $this;
    }

    /**
     * Get nroHistoria
     *
     * @return integer 
     */
    public function getNroHistoria()
    {
        return $this->nroHistoria;
    }

    /**
     * Set nroNota
     *
     * @param integer $nroNota
     * @return SgtSatelite
     */
    public function setNroNota($nroNota)
    {
        $this->nroNota = $nroNota;
    
        return $this;
    }

    /**
     * Get nroNota
     *
     * @return integer 
     */
    public function getNroNota()
    {
        return $this->nroNota;
    }
}