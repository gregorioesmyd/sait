<?php

namespace Frontend\VideotecaBundle\Entity\WrapperSegmento;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SgtVideoteca
 *
 * @ORM\Table(name="videoteca.sgt_videoteca")
 * @ORM\Entity
 */
class SgtVideoteca extends Segmento
{

    const CLASE = "SgtVideoteca";

    /**
     * @var string
     *
     * @ORM\Column(name="tituloOriginal", type="string", length=255, nullable=true)
     */
    private $tituloOriginal;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="El campo Año Realizado evento no puede estar vacio")
     * @ORM\Column(name="anioRealizado", type="string", length=4)
     */
    private $anioRealizado;

    /**
     * @var string
     *
     * @ORM\Column(name="productor", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="El campo productor no puede estar vacio")
     */
    private $productor;

    /**
     * @var string
     *
     * @ORM\Column(name="director", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="El campo director no puede estar vacio")
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="proveedor", type="string", length=60, nullable=true)
     * @Assert\NotBlank(message="El campo proveedor no puede estar vacio")
     */
    private $proveedor;

    /**
     * @var \Autoridad
     *
     * @Assert\NotBlank(message="El campo autoridad no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Autoridad")
     * @ORM\JoinColumn(name="autoridad_id", referencedColumnName="id")
     */
    private $autoridad;

    /**
     * @var \Date
     *
     * @Assert\Date()
     * @Assert\NotBlank(message="El campo Fecha Ingreso no puede estar vacio")
     *
     * @ORM\Column(name="fechaIngreso", type="date")
     */
    private $fechaIngreso;

    /**
     * @var string
     *
     * @ORM\Column(name="evaluador", type="string", length=60, nullable=true)
     */
    private $evaluador;

    /**
     * @Assert\NotBlank(message="El campo Pais Productor evento no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Paises")
     * @ORM\JoinColumn(name="pais_productor_id", referencedColumnName="id")
     */
    private $paisProductor;

    /**
     * @Assert\NotBlank(message="El campo genero evento no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Genero")
     * @ORM\JoinColumn(name="genero_id", referencedColumnName="id")
     */
    private $genero;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Franja")
     * @ORM\JoinColumn(name="franja_id", referencedColumnName="id")
     */
    private $franja;

    /**
     * @Assert\NotBlank(message="El campo clasificacion evento no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Clasificacion")
     * @ORM\JoinColumn(name="clasificacion_id", referencedColumnName="id")
     */
    private $clasificacion;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Elemento")
     * @ORM\JoinColumn(name="elemento_id", referencedColumnName="id")
     */
    private $elemento;

    /**
     * @Assert\NotBlank(message="El campo tqc evento no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\TQC")
     * @ORM\JoinColumn(name="tqc_id", referencedColumnName="id")
     */
    private $tqc;

    /**
     * @Assert\NotBlank(message="El campo norma evento no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Norma")
     * @ORM\JoinColumn(name="norma_id", referencedColumnName="id")
     */
    private $norma;

    /**
     * Set tituloOriginal
     *
     * @param string $tituloOriginal
     * @return SgtVideoteca
     */
    public function setTituloOriginal($tituloOriginal)
    {
        $this->tituloOriginal = $tituloOriginal;

        return $this;
    }

    /**
     * Get tituloOriginal
     *
     * @return string
     */
    public function getTituloOriginal()
    {
        return $this->tituloOriginal;
    }

    /**
     * Set anioRealizado
     *
     * @param string $anioRealizado
     * @return SgtVideoteca
     */
    public function setAnioRealizado($anioRealizado)
    {
        $this->anioRealizado = $anioRealizado;

        return $this;
    }

    /**
     * Get anioRealizado
     *
     * @return string
     */
    public function getAnioRealizado()
    {
        return $this->anioRealizado;
    }

    /**
     * Set productor
     *
     * @param string $productor
     * @return SgtVideoteca
     */
    public function setProductor($productor)
    {
        $this->productor = $productor;

        return $this;
    }

    /**
     * Get productor
     *
     * @return string
     */
    public function getProductor()
    {
        return $this->productor;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return SgtVideoteca
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set proveedor
     *
     * @param string $proveedor
     * @return SgtVideoteca
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return string
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set fechaIngreso
     *
     * @param \Date $fechaIngreso
     * @return SgtVideoteca
     */
    public function setFechaIngreso($fechaIngreso)
    {
        $this->fechaIngreso = $fechaIngreso;

        return $this;
    }

    /**
     * Get fechaIngreso
     *
     * @return \Date
     */
    public function getFechaIngreso()
    {
        return $this->fechaIngreso;
    }

    /**
     * Set evaluador
     *
     * @param string $evaluador
     * @return SgtVideoteca
     */
    public function setEvaluador($evaluador)
    {
        $this->evaluador = $evaluador;

        return $this;
    }

    /**
     * Get evaluador
     *
     * @return string
     */
    public function getEvaluador()
    {
        return $this->evaluador;
    }

    /**
     * Set norma
     *
     * @param string $norma
     * @return SgtVideoteca
     */
    public function setNorma($norma)
    {
        $this->norma = $norma;

        return $this;
    }

    /**
     * Get norma
     *
     * @return string
     */
    public function getNorma()
    {
        return $this->norma;
    }

    /**
     * Set genero
     *
     * @param \Frontend\VideotecaBundle\Entity\Genero $genero
     * @return SgtVideoteca
     */
    public function setGenero(\Frontend\VideotecaBundle\Entity\Genero $genero = null)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return \Frontend\VideotecaBundle\Entity\Genero
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set franja
     *
     * @param \Frontend\VideotecaBundle\Entity\Franja $franja
     * @return SgtVideoteca
     */
    public function setFranja(\Frontend\VideotecaBundle\Entity\Franja $franja = null)
    {
        $this->franja = $franja;

        return $this;
    }

    /**
     * Get franja
     *
     * @return \Frontend\VideotecaBundle\Entity\Franja
     */
    public function getFranja()
    {
        return $this->franja;
    }

    /**
     * Set clasificacion
     *
     * @param \Frontend\VideotecaBundle\Entity\Clasificacion $clasificacion
     * @return SgtVideoteca
     */
    public function setClasificacion(\Frontend\VideotecaBundle\Entity\Clasificacion $clasificacion = null)
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return \Frontend\VideotecaBundle\Entity\Clasificacion
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }

    /**
     * Set elemento
     *
     * @param \Frontend\VideotecaBundle\Entity\Elemento $elemento
     * @return SgtVideoteca
     */
    public function setElemento(\Frontend\VideotecaBundle\Entity\Elemento $elemento = null)
    {
        $this->elemento = $elemento;

        return $this;
    }

    /**
     * Get elemento
     *
     * @return \Frontend\VideotecaBundle\Entity\Elemento
     */
    public function getElemento()
    {
        return $this->elemento;
    }

    /**
     * Set tqc
     *
     * @param \Frontend\VideotecaBundle\Entity\TQC $tqc
     * @return SgtVideoteca
     */
    public function setTqc(\Frontend\VideotecaBundle\Entity\TQC $tqc = null)
    {
        $this->tqc = $tqc;

        return $this;
    }

    /**
     * Get tqc
     *
     * @return \Frontend\VideotecaBundle\Entity\TQC
     */
    public function getTqc()
    {
        return $this->tqc;
    }

    /**
     * Set paisProductor
     *
     * @param \Frontend\VideotecaBundle\Entity\Paises $paisProductor
     * @return SgtVideoteca
     */
    public function setPaisProductor(\Frontend\VideotecaBundle\Entity\Paises $paisProductor = null)
    {
        $this->paisProductor = $paisProductor;

        return $this;
    }

    /**
     * Get paisProductor
     *
     * @return \Frontend\VideotecaBundle\Entity\Paises
     */
    public function getPaisProductor()
    {
        return $this->paisProductor;
    }

    /**
     * Set autoridad
     *
     * @param \Frontend\VideotecaBundle\Entity\Autoridad $autoridad
     * @return SgtVideoteca
     */
    public function setAutoridad(\Frontend\VideotecaBundle\Entity\Autoridad $autoridad = null)
    {
        $this->autoridad = $autoridad;
    
        return $this;
    }

    /**
     * Get autoridad
     *
     * @return \Frontend\VideotecaBundle\Entity\Autoridad 
     */
    public function getAutoridad()
    {
        return $this->autoridad;
    }
}
