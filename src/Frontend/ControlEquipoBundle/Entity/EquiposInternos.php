<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EquiposInternos
 *
 * @ORM\Table(name="controlequipo.equipos_internos")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields={"serial"},
 *      message="El serial ya existe."
 * )
 * @UniqueEntity(
 *      fields={"codigoBarra"},
 *      message="El codigo de barras que ingresó ya existe.")
 */
class EquiposInternos 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.equipos_internos_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_catalogo", type="string", length=10, nullable=true)
     * @Assert\NotBlank(message="El codigo del catálogo no puede estar en blanco.")
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "El codigo del catálogo debe contener 10 caracteres.",
     * )
     * @Assert\Regex(
     *     pattern="/.{5}-{1}.{4}/",
     *     match=true,
     *     message="El codigo del catálogo no tiene la estructura requerida xxxxx-xxxx."
     * )
     */
     private $codigoCatalogo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_bien", type="string", length=12, nullable=true, unique=true)
     * @Assert\NotBlank(message="El codigo del bien no puede estar en blanco.")
     * @Assert\Length(
     *      min = 12,
     *      max = 12,
     *      exactMessage = "El codigo del bien debe contener 12 caracteres.",
     * )
     * @Assert\Regex(
     *     pattern="/BN-VZLA-.{4}/",
     *     match=true,
     *     message="El codigo del bien no tiene la estructura requerida BN-VZLA-XXXX."
     * )
     */
     private $codigoBien;

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
     * @Assert\NotBlank(message="Código Barra/ Nro Control no puede estar en blanco.")
     */
    private $codigoBarra;

    /**
     * @var string
     *
     * @ORM\Column(name="serial", type="string", length=50, nullable=false, unique=true)
     * @Assert\NotBlank(message="El serial no puede estar en blanco.")
     */
    private $serial;

    /**
     * @var \ResponsablePatrimonial
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="equipos_internos")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="responsablePatrimonial_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\NotNull(message="Debe seleccionar El Responsable Patrimonial")
     */
    private $responsablePatrimonial;
    
    /**
     * @var \ResponsableUso
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="equipos_internos")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="responsableUso_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\NotBlank(message="Debe seleccionar a el responsable de uso")
     */
    private $responsableUso;

    /**
     * @var \Date
     *
     * @ORM\Column(name="control_perceptivo", type="date", nullable=true)
     * @Assert\Date(message="Formato de fecha incorrecta.")
     */
    private $controlPerceptivo;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud_contratacion", type="string", length=20, nullable=true)
     */
    private $solicitudContratacion;

    /**
     * @var string
     *
     * @ORM\Column(name="partida_presupuestaria", type="string", length=20, nullable=true)
     */
    private $partidaPresupuestaria;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_factura", type="string", length=15, nullable=true)
     */
    private $nroFactura;


    /**
     * @var string
     *
     * @ORM\Column(name="acta_entrega", type="string", length=20, nullable=true)
     */
    private $actaEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="orden_compra", type="string", length=50, nullable=true)
     */
    private $ordenCompra;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_orden_compra", type="date", nullable=true)
     * @Assert\Date(message="Formato de fecha incorrecta.")
     */
    private $fechaOrdenCompra;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="creado", type="date", nullable=true)
     */
    private $creado;

    /**
     * @Assert\File(maxSize="5000000", maxSizeMessage="El archivo que intenta subir es demasiado grande.")
     *  
     */
    private $file;
    
    /**
     *  Marca del Equipo, solo para referencia
     * @Assert\NotNull(message="Seleccione una Marca")
     */
    private $marca;
    
    /**
     * @var string
     *
     * @ORM\Column(name="foto_referencia", type="string", length=500, nullable=true)
     */
    private $fotoReferencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=true)
     */
    private $estatus=1;

    
    /**
     * @var \Monedas
     *
     * @ORM\ManyToOne(targetEntity="Monedas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $moneda;
    
    /**
     * @var string
     *
     * @ORM\Column(name="precio", type="decimal", nullable=true)
     * @Assert\Type(type="numeric", message="Precio no valido, verifique.")
     */
    private $precio;
    
    /**
     * @var \Modelos
     *
     * @ORM\ManyToOne(targetEntity="Modelos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modelo_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Seleccione un Modelo")
     */
    private $modelo;

    /**
     * @var \Proveedores
     *
     * @ORM\ManyToOne(targetEntity="Proveedores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proveedor_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Seleccione un Proveedor")
     */
    private $proveedor;
    
    /**
     * @var \Proveedores
     *
     * @ORM\ManyToOne(targetEntity="UbicacionFisica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ubicacionFisica_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Seleccione una ubicacion fisica")

     */
    private $ubicacionFisica;
    
    /**
     * @var \Proveedores
     *
     * @ORM\ManyToOne(targetEntity="UbicacionAdministrativa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ubicacionAdministrativa_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Seleccione una ubicacion administrativa")
     * 
     */
    private $ubicacionAdministrativa;
    
    /**
     * @var string
     *
     * @ORM\Column(name="anio", type="integer", nullable=true)
     */
    private $anio;
    
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=500, nullable=true)
     */
    private $color;

        /**
     * @var string
     *
     * @ORM\Column(name="numero_piezas", type="integer", nullable=true)
     */
    private $numeroPiezas;
    
        /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;
    
  
    /**
     * @var string
     *
     * @ORM\Column(name="valor_inicial", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $valorInicial;
    
    /**
     * @var string
     *
     * @ORM\Column(name="depreciacion_mensual", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $depreciacionMensual;
    
    /**
     * @var string
     *
     * @ORM\Column(name="depreciacion_acumulada", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $depreciacionAcumulada;
    
    /**
     * @var string
     *
     * @ORM\Column(name="valor_actual", type="decimal", precision=20, scale= 2, nullable=true)
     */
    private $valorActual;
    
    /**
     * @var \ResponsableRegistro
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="equipos_internos")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="responsableRegistro_id", referencedColumnName="id")
     *   }
     * )
     */
    private $responsableRegistro;
    
    /*
     * Getters and Setters
     */

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
     * Sets marca.
     *
     * @param $marca
     */
    public function setMarca($marca = null)
    {
        $this->marca = $marca;
    }
    
    /**
     * Get marca.
     *
     * @return UploadedFile
     */
    public function getMarca()
    {
        return $this->marca;
    }
    
    /**
     * Set descripcionEquipo
     *
     * @param string $descripcionEquipo
     * @return EquiposInternos
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
     * @return EquiposInternos
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
     * @return EquiposInternos
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
     * Set responsablePatrimonial
     *
     * @param integer $responsablePatrimonial
     * @return EquiposInternos
     */
    public function setResponsablePatrimonial(\Administracion\UsuarioBundle\Entity\Perfil $responsablePatrimonial = null)
    {
        $this->responsablePatrimonial = $responsablePatrimonial;
    
        return $this;
    }
    
    /**
     * Get responsablePatrimonial
     *
     * @return EquiposInternos
     */
    public function getResponsablePatrimonial()
    {
        return $this->responsablePatrimonial;
    }

    /**
     * Set controlPerceptivo
     *
     * @param \Date $controlPerceptivo
     * @return EquiposInternos
     */
    public function setControlPerceptivo($controlPerceptivo)
    {
        $this->controlPerceptivo = $controlPerceptivo;
    
        return $this;
    }

    /**
     * Get controlPerceptivo
     *
     * @return \Date 
     */
    public function getControlPerceptivo()
    {
        return $this->controlPerceptivo;
    }

    /**
     * Set solicitudContratacion
     *
     * @param string $solicitudContratacion
     * @return EquiposInternos
     */
    public function setSolicitudContratacion($solicitudContratacion)
    {
        $this->solicitudContratacion = $solicitudContratacion;
    
        return $this;
    }

    /**
     * Get solicitudContratacion
     *
     * @return string 
     */
    public function getSolicitudContratacion()
    {
        return $this->solicitudContratacion;
    }

    /**
     * Set partidaPresupuestaria
     *
     * @param string $partidaPresupuestaria
     * @return EquiposInternos
     */
    public function setPartidaPresupuestaria($partidaPresupuestaria)
    {
        $this->partidaPresupuestaria = $partidaPresupuestaria;
    
        return $this;
    }

    /**
     * Get partidaPresupuestaria
     *
     * @return string 
     */
    public function getPartidaPresupuestaria()
    {
        return $this->partidaPresupuestaria;
    }

    /**
     * Set nroFactura
     *
     * @param string $nroFactura
     * @return EquiposInternos
     */
    public function setNroFactura($nroFactura)
    {
        $this->nroFactura = $nroFactura;
    
        return $this;
    }

    /**
     * Get nroFactura
     *
     * @return string 
     */
    public function getNroFactura()
    {
        return $this->nroFactura;
    }

    /**
     * Set precio
     *
     * @param string $precio
     * @return EquiposInternos
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    
        return $this;
    }

    /**
     * Get precio
     *
     * @return string 
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set actaEntrega
     *
     * @param string $actaEntrega
     * @return EquiposInternos
     */
    public function setActaEntrega($actaEntrega)
    {
        $this->actaEntrega = $actaEntrega;
    
        return $this;
    }

    /**
     * Get actaEntrega
     *
     * @return string 
     */
    public function getActaEntrega()
    {
        return $this->actaEntrega;
    }

    /**
     * Set creado
     *
     * @param \Date $creado
     * @return EquiposInternos
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;
    
        return $this;
    }

    /**
     * Get creado
     *
     * @return \Date 
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set fotoReferencia
     *
     * @param string $fotoReferencia
     * @return EquiposInternos
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
     * @return EquiposInternos
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
    public function getOrdenCompra()
    {
        return $this->ordenCompra;
    }
    
     /**
     * Set ordenCompra
     *
     * @param integer $ordenCompra
     * @return EquiposInternos
     */
    public function setOrdenCompra($ordenCompra)
    {
        $this->ordenCompra = $ordenCompra;
    
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
     * Set moneda
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Monedas $moneda
     * @return EquiposInternos
     */
    public function setMoneda(\Frontend\ControlEquipoBundle\Entity\Monedas $moneda = null)
    {
        $this->moneda = $moneda;
    
        return $this;
    }

    /**
     * Get moneda
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Monedas 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set modelo
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Modelos $modelo
     * @return EquiposInternos
     */
    public function setModelo(\Frontend\ControlEquipoBundle\Entity\Modelos $modelo = null)
    {
        $this->modelo = $modelo;
    
        return $this;
    }

    /**
     * Get modelo
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Modelos 
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set proveedor
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Proveedores $proveedor
     * @return EquiposInternos
     */
    public function setProveedor(\Frontend\ControlEquipoBundle\Entity\Proveedores $proveedor = null)
    {
        $this->proveedor = $proveedor;
    
        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \Frontend\ControlEquipoBundle\Entity\Proveedores 
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
    
    public function __toString() {
        return $this->descripcionEquipo;
    }

    /**
     * Set codigoCatalogo
     *
     * @param string $codigoCatalogo
     * @return EquiposInternos
     */
    public function setCodigoCatalogo($codigoCatalogo)
    {
        $this->codigoCatalogo = $codigoCatalogo;
    
        return $this;
    }

    /**
     * Get codigoCatalogo
     *
     * @return string 
     */
    public function getCodigoCatalogo()
    {
        return $this->codigoCatalogo;
    }

    /**
     * Set codigoBien
     *
     * @param string $codigoBien
     * @return EquiposInternos
     */
    public function setCodigoBien($codigoBien)
    {
        $this->codigoBien = $codigoBien;
    
        return $this;
    }

    /**
     * Get codigoBien
     *
     * @return string 
     */
    public function getCodigoBien()
    {
        return $this->codigoBien;
    }

    /**
     * Set fechOrdenCompra
     *
     * @param \DateTime $fechOrdenCompra
     * @return EquiposInternos
     */
    public function setFechOrdenCompra($fechOrdenCompra)
    {
        $this->fechOrdenCompra = $fechOrdenCompra;
    
        return $this;
    }

    /**
     * Get fechOrdenCompra
     *
     * @return \DateTime 
     */
    public function getFechOrdenCompra()
    {
        return $this->fechOrdenCompra;
    }

    /**
     * Set fechaOrdenCompra
     *
     * @param \DateTime $fechaOrdenCompra
     * @return EquiposInternos
     */
    public function setFechaOrdenCompra($fechaOrdenCompra)
    {
        $this->fechaOrdenCompra = $fechaOrdenCompra;
    
        return $this;
    }

    /**
     * Get fechaOrdenCompra
     *
     * @return \DateTime 
     */
    public function getFechaOrdenCompra()
    {
        return $this->fechaOrdenCompra;
    }

    /**
     * Set ubicacionFisica
     *
     * @param \Frontend\ControlEquipoBundle\Entity\UbicacionFisica $ubicacionFisica
     * @return EquiposInternos
     */
    public function setUbicacionFisica(\Frontend\ControlEquipoBundle\Entity\UbicacionFisica $ubicacionFisica = null)
    {
        $this->ubicacionFisica = $ubicacionFisica;
    
        return $this;
    }

    /**
     * Get ubicacionFisica
     *
     * @return \Frontend\ControlEquipoBundle\Entity\UbicacionFisica 
     */
    public function getUbicacionFisica()
    {
        return $this->ubicacionFisica;
    }

    /**
     * Set ubicacionAdministrativa
     *
     * @param \Frontend\ControlEquipoBundle\Entity\UbicacionAdministrativa $ubicacionAdministrativa
     * @return EquiposInternos
     */
    public function setUbicacionAdministrativa(\Frontend\ControlEquipoBundle\Entity\UbicacionAdministrativa $ubicacionAdministrativa = null)
    {
        $this->ubicacionAdministrativa = $ubicacionAdministrativa;
    
        return $this;
    }

    /**
     * Get ubicacionAdministrativa
     *
     * @return \Frontend\ControlEquipoBundle\Entity\UbicacionAdministrativa 
     */
    public function getUbicacionAdministrativa()
    {
        return $this->ubicacionAdministrativa;
    }

    /**
     * Set responsableUso
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsableUso
     * @return EquiposInternos
     */
    public function setResponsableUso(\Administracion\UsuarioBundle\Entity\Perfil $responsableUso = null)
    {
        $this->responsableUso = $responsableUso;
    
        return $this;
    }

    /**
     * Get responsableUso
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getResponsableUso()
    {
        return $this->responsableUso;
    }

    /**
     * Set anio
     *
     * @param \intener $anio
     * @return EquiposInternos
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;
    
        return $this;
    }

    /**
     * Get anio
     *
     * @return \intener 
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return EquiposInternos
     */
    public function setColor($color)
    {
        $this->color = $color;
    
        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set numeroPiezas
     *
     * @param integer $numeroPiezas
     * @return EquiposInternos
     */
    public function setNumeroPiezas($numeroPiezas)
    {
        $this->numeroPiezas = $numeroPiezas;
    
        return $this;
    }

    /**
     * Get numeroPiezas
     *
     * @return integer 
     */
    public function getNumeroPiezas()
    {
        return $this->numeroPiezas;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return EquiposInternos
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set costo
     *
     * @param string $costo
     * @return EquiposInternos
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;
    
        return $this;
    }

    /**
     * Get costo
     *
     * @return string 
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * Set valorInicial
     *
     * @param string $valorInicial
     * @return EquiposInternos
     */
    public function setValorInicial($valorInicial)
    {
        $this->valorInicial = $valorInicial;
    
        return $this;
    }

    /**
     * Get valorInicial
     *
     * @return string 
     */
    public function getValorInicial()
    {
        return $this->valorInicial;
    }

    /**
     * Set depreciacionMensual
     *
     * @param string $depreciacionMensual
     * @return EquiposInternos
     */
    public function setDepreciacionMensual($depreciacionMensual)
    {
        $this->depreciacionMensual = $depreciacionMensual;
    
        return $this;
    }

    /**
     * Get depreciacionMensual
     *
     * @return string 
     */
    public function getDepreciacionMensual()
    {
        return $this->depreciacionMensual;
    }

    /**
     * Set depreciacionAcumulada
     *
     * @param string $depreciacionAcumulada
     * @return EquiposInternos
     */
    public function setDepreciacionAcumulada($depreciacionAcumulada)
    {
        $this->depreciacionAcumulada = $depreciacionAcumulada;
    
        return $this;
    }

    /**
     * Get depreciacionAcumulada
     *
     * @return string 
     */
    public function getDepreciacionAcumulada()
    {
        return $this->depreciacionAcumulada;
    }

    /**
     * Set valorActual
     *
     * @param string $valorActual
     * @return EquiposInternos
     */
    public function setValorActual($valorActual)
    {
        $this->valorActual = $valorActual;
    
        return $this;
    }

    /**
     * Get valorActual
     *
     * @return string 
     */
    public function getValorActual()
    {
        return $this->valorActual;
    }

    /**
     * Set responsableRegistro
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $responsableRegistro
     * @return EquiposInternos
     */
    public function setResponsableRegistro(\Administracion\UsuarioBundle\Entity\Perfil $responsableRegistro = null)
    {
        $this->responsableRegistro = $responsableRegistro;
    
        return $this;
    }

    /**
     * Get responsableRegistro
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getResponsableRegistro()
    {
        return $this->responsableRegistro;
    }
}