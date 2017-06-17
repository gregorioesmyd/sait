<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EquiposPautas
 *
 * @ORM\Table(name="controlequipo.equipos_pautas")
 * @ORM\Entity
 */
class EquiposPautas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.equipos_pautas_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="chequeo_entrada", type="boolean", nullable=false)
     */
    private $chequeoEntrada;

    /**
     * @var boolean
     *
     * @ORM\Column(name="chequeo_salida", type="boolean", nullable=false)
     */
    private $chequeoSalida;

    /**
     * @var \Pautas
     *
     * @ORM\ManyToOne(targetEntity="Pautas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pauta_id", referencedColumnName="id")
     * })
     */
    private $pauta;

    /**
     * @var \EquiposInternos
     *
     * @ORM\ManyToOne(targetEntity="EquiposInternos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_interno_id", referencedColumnName="id")
     * })
     */
    private $equipoInterno;

    /**
     * @var integer
     *
     * @ORM\Column(name="equipo_interno_id", type="integer", nullable=false)
     */
    private $equipoId;


    /**
     * @var integer
     *
     * @ORM\Column(name="pauta_id", type="integer", nullable=false)
     */
    private $pautaId;
    
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
     * Get equipoId
     *
     * @return integer 
     */
    public function getEquipoId()
    {
        return $this->equipoId;
    }

    /**
     * Get pautaId
     *
     * @return integer 
     */
    public function getPautaId()
    {
        return $this->pautaId;
    }
    
    /**
     * Set chequeoEntrada
     *
     * @param boolean $chequeoEntrada
     * @return EquiposPautas
     */
    public function setChequeoEntrada($chequeoEntrada)
    {
        $this->chequeoEntrada = $chequeoEntrada;
    
        return $this;
    }

    /**
     * Get chequeoEntrada
     *
     * @return boolean 
     */
    public function getChequeoEntrada()
    {
        return $this->chequeoEntrada;
    }

    /**
     * Set chequeoSalida
     *
     * @param boolean $chequeoSalida
     * @return EquiposPautas
     */
    public function setChequeoSalida($chequeoSalida = false)
    {
        $this->chequeoSalida = $chequeoSalida;
    
        return $this;
    }

    /**
     * Get chequeoSalida
     *
     * @return boolean 
     */
    public function getChequeoSalida()
    {
        return $this->chequeoSalida;
    }

    /**
     * Set pauta
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Pautas $pauta
     * @return EquiposPautas
     */
    public function setPauta(\Frontend\ControlEquipoBundle\Entity\Pautas $pauta = null)
    {
        $this->pauta = $pauta;
    
        return $this;
    }

    /**
     * Get pauta
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Pautas 
     */
    public function getPauta()
    {
        return $this->pauta;
    }

    /**
     * Set equipoInterno
     *
     * @param \Frontend\ControlEquipoBundle\Entity\EquiposInternos $equipoInterno
     * @return EquiposPautas
     */
    public function setEquipoInterno(\Frontend\ControlEquipoBundle\Entity\EquiposInternos $equipoInterno = null)
    {
        $this->equipoInterno = $equipoInterno;
    
        return $this;
    }

    /**
     * Get equipoInterno
     *
     * @return \Frontend\ControlEquipoBundle\Entity\EquiposInternos 
     */
    public function getEquipoInterno()
    {
        return $this->equipoInterno;
    }
}