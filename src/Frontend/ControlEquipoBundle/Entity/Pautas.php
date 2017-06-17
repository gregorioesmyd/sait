<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Pautas
 *
 * @ORM\Table(name="controlequipo.pautas")
 * @ORM\Entity
 */
class Pautas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.pautas_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     * @Assert\NotNull(message="Indique la fecha Inicio de la Pauta")
     */
    private $fechaDesde;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     * @Assert\NotNull(message="Indique la fecha Final de la Pauta")
     */
    private $fechaHasta;

    /**
     * @var \responsable
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="pautas")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\NotNull(message="Seleccione el Responsable de la Pauta")
     */
    private $responsable;

    /**
     * @var string
     *
     * @ORM\Column(name="justificacion", type="string", length=500, nullable=false)
     * @Assert\NotNull(message="Indique el Evento / Destino")
     */
    private $justificacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aprobado", type="boolean", nullable=false)
     */
    private $aprobado;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_pauta", type="integer", nullable=false)
     * @Assert\NotNull(message="Indique el Tipo de Pauta")
     */
    private $tipoPauta;

     /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
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
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return Pautas
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaPauta
     *
     * @return \Date
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return Pautas
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \Date
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set responsable
     *
     * @param integer $responsable
     * @return Pautas
     */
    public function setResponsable(\Administracion\UsuarioBundle\Entity\Perfil $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return Pautas
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set justificacion
     *
     * @param string $justificacion
     * @return Pautas
     */
    public function setJustificacion($justificacion)
    {
        $this->justificacion = $justificacion;

        return $this;
    }

    /**
     * Get justificacion
     *
     * @return string
     */
    public function getJustificacion()
    {
        return $this->justificacion;
    }

    /**
     * Set aprobado
     *
     * @param boolean $aprobado
     * @return Pautas
     */
    public function setAprobado($aprobado)
    {
        $this->aprobado = $aprobado;

        return $this;
    }

    /**
     * Get aprobado
     *
     * @return boolean
     */
    public function getAprobado()
    {
        return $this->aprobado;
    }

    /**
     * Set tipoPauta
     *
     * @param integer
     * @return Pautas
     */
    public function setTipoPauta($tipoPauta)
    {
        $this->tipoPauta = $tipoPauta;

        return $this;
    }

    /**
     * Get tipoPauta
     *
     * @return integer
     */
    public function getTipoPauta()
    {
        return $this->tipoPauta;
    }

     /**
     * Set estatus
     *
     * @param integer
     * @return Pautas
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return integer
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    public function __toString() {
        return $this->justificacion;
    }
}
