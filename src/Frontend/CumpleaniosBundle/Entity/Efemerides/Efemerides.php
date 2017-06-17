<?php

namespace Frontend\CumpleaniosBundle\Entity\Efemerides;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Efemerides
 *
 * @ORM\Table(name="efemerides.efemerides")
 * @ORM\Entity
 */
class Efemerides {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="efemerides.efemerides_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank(message="El campo nombre no puede estar en blanco.").
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="anio", type="string", nullable=true)
     */
    private $anio;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", nullable=false)
     * @Assert\NotBlank(message="Debe seleccionar un mes.").
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="dia", type="string", nullable=false)
     * @Assert\NotBlank(message="El campo dia no puede estar en blanco.").
     */
    private $dia;

    /**
     * @var \Frontend\CumpleaniosBundle\Entity\Efemerides\TipoEfemerides
     *
     * @ORM\ManyToOne(targetEntity="Frontend\CumpleaniosBundle\Entity\Efemerides\TipoEfemerides")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipoefemerides", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un tipo.")
     */
    private $tipoId;

    /**
     * @var \Frontend\CumpleaniosBundle\Entity\Efemerides\Pais
     *
     * @ORM\ManyToOne(targetEntity="Frontend\CumpleaniosBundle\Entity\Efemerides\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pais", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un pais.")
     */
    private $paisId;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Efemerides
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set anio
     *
     * @param string $anio
     * @return Efemerides
     */
    public function setAnio($anio) {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string 
     */
    public function getAnio() {
        return $this->anio;
    }

    /**
     * Set mes
     *
     * @param string $mes
     * @return Efemerides
     */
    public function setMes($mes) {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string 
     */
    public function getMes() {
        return $this->mes;
    }

    /**
     * Set dia
     *
     * @param string $dia
     * @return Efemerides
     */
    public function setDia($dia) {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return string 
     */
    public function getDia() {
        return $this->dia;
    }

    /**
     * Set tipoId
     *
     * @param \Frontend\CumpleaniosBundle\Entity\Efemerides\TipoEfemerides $tipoId
     * @return Efemerides
     */
    public function setTipoId(\Frontend\CumpleaniosBundle\Entity\Efemerides\TipoEfemerides $tipoId = null) {
        $this->tipoId = $tipoId;

        return $this;
    }

    /**
     * Get tipoId
     *
     * @return \Frontend\CumpleaniosBundle\Entity\Efemerides\TipoEfemerides 
     */
    public function getTipoId() {
        return $this->tipoId;
    }

    /**
     * Set paisId
     *
     * @param \Frontend\CumpleaniosBundle\Entity\Efemerides\Pais $paisId
     * @return Efemerides
     */
    public function setPaisId(\Frontend\CumpleaniosBundle\Entity\Efemerides\Pais $paisId = null) {
        $this->paisId = $paisId;

        return $this;
    }

    /**
     * Get paisId
     *
     * @return \Frontend\CumpleaniosBundle\Entity\Efemerides\Pais 
     */
    public function getPaisId() {
        return $this->paisId;
    }

    public function __toString() {
        return $this->getNombre();
    }

}
