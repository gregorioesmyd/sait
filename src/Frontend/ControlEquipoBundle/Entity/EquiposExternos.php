<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EquiposExternos
 *
 * @ORM\Table(name="controlequipo.equipos_externos")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields={"serial"},
 *      message="El serial ya existe."
 * )
 * @UniqueEntity(
 *      fields={"codigoBarra"},
 *      message="El codigo de barras que ingresó ya existe.")
 */
class EquiposExternos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.equipos_externos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;



    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_equipo", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="La descripción del Equipo no puede estar en blanco.")
     */
    private $descripcionEquipo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barra", type="string", length=50, nullable=false, unique=true)
     * @Assert\NotBlank(message="El Codigo de Barra no puede estar en blanco.")
     * @Assert\Length(min="6", minMessage="El código de barra debe tener {{ limit }} caracteres como mínimo.")
     * @Assert\Regex(
     *     pattern="/^[-\w\.]+$/i",
     *     match=true,
     *     message="El código de barra contiene un caracter no válido."
     * )
     */
    private $codigoBarra;

    /**
     * @var string
     *
     * @ORM\Column(name="serial", type="string", length=50, nullable=false, unique=true)
     * @Assert\NotBlank(message="El serial no puede estar en blanco.")
     * @Assert\Length(min="6", minMessage="El serial debe tener {{ limit }} caracteres como mínimo.")
     * @Assert\Regex(
     *     pattern="/^[-\w\.]+$/i",
     *     match=true,
     *     message="El serial contiene un caracter no válido."
     * )
     */
    private $serial;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="creado", type="datetime", nullable=true)
     */
    private $creado;

    /**
     * @Assert\File(maxSize="5000000", maxSizeMessage="El archivo que intenta subir es demasiado grande.")
     *
     */
    private $file;
    /**
     * @var string
     *
     * @ORM\Column(name="foto_referencia", type="string", length=150, nullable=true)
     */
    private $fotoReferencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus=1;

    /**
     * @var integer
     *
     * @ORM\Column(name="propietario_id", type="integer", nullable=false)
     * @Assert\NotBlank(message="Debe buscar el Propietario Interno/Externo")
     */
    private $propietarioId;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_propietario", type="integer", nullable=false)
     * @Assert\NotBlank(message="Debe indicar el tipo de propietario (Interno/Externo)")
     */
    private $tipoPropietario;

    /**
     * @var \Marcas
     *
     * @ORM\ManyToOne(targetEntity="Marcas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_marca_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Seleccione una Marca")
     */
    private $equipoMarca;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

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
     * Set descripcionEquipo
     *
     * @param string $descripcionEquipo
     * @return EquiposExternos
     */
    public function setDescripcionEquipo($descripcionEquipo)
    {
        $this->descripcionEquipo = $descripcionEquipo;

        return $this;
    }

    /**
     * Get descripcionEquipo
     *
     * @return string
     */
    public function getDescripcionEquipo()
    {
        return $this->descripcionEquipo;
    }

    /**
     * Set codigoBarra
     *
     * @param string $codigoBarra
     * @return EquiposExternos
     */
    public function setCodigoBarra($codigoBarra)
    {
        $this->codigoBarra = $codigoBarra;

        return $this;
    }

    /**
     * Get codigoBarra
     *
     * @return string
     */
    public function getCodigoBarra()
    {
        return $this->codigoBarra;
    }

    /**
     * Set serial
     *
     * @param string $serial
     * @return EquiposExternos
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return EquiposExternos
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set fotoReferencia
     *
     * @param string $fotoReferencia
     * @return EquiposExternos
     */
    public function setFotoReferencia($fotoReferencia)
    {
        $this->fotoReferencia = $fotoReferencia;

        return $this;
    }

    /**
     * Get fotoReferencia
     *
     * @return string
     */
    public function getFotoReferencia()
    {
        return $this->fotoReferencia;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return EquiposExternos
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

    /**
     * Set propietarioId
     *
     * @param integer $propietarioId
     * @return EquiposExternos
     */
    public function setPropietarioId($propietarioId)
    {
        $this->propietarioId = $propietarioId;

        return $this;
    }

    /**
     * Get propietarioId
     *
     * @return integer
     */
    public function getPropietarioId()
    {
        return $this->propietarioId;
    }

    /**
     * Set tipoPropietario
     *
     * @param integer $tipoPropietario
     * @return EquiposExternos
     */
    public function setTipoPropietario($tipoPropietario)
    {
        $this->tipoPropietario = $tipoPropietario;

        return $this;
    }

    /**
     * Get tipoPropietario
     *
     * @return integer
     */
    public function getTipoPropietario()
    {
        return $this->tipoPropietario;
    }

    /**
     * Set equipoMarca
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Marcas $equipoMarca
     * @return EquiposExternos
     */
    public function setEquipoMarca(\Frontend\ControlEquipoBundle\Entity\Marcas $equipoMarca = null)
    {
        $this->equipoMarca = $equipoMarca;

        return $this;
    }

    /**
     * Get equipoMarca
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Marcas
     */
    public function getEquipoMarca()
    {
        return $this->equipoMarca;
    }

    public function __toString() {
        return $this->descripcionEquipo;
    }
}
