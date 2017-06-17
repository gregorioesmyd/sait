<?php

namespace Frontend\EstudioCabinaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estudiocabina.reservaciones
 *
 * @ORM\Table(name="estudiocabina.reservaciones")
 * @ORM\Entity
 */
class Reservaciones
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="estudiocabina.reservaciones_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="perfil_id", type="integer", nullable=false)
     */
    private $perfilId;

    /**
     * @var string
     *
     * @ORM\Column(name="pauta", type="string", nullable=false)
     * @Assert\NotBlank(message="La pauta de la reservación no puede estar en blanco.")
     */
    private $pauta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     * @Assert\NotBlank(message="La fecha no puede estar en blanco.")
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horainicio", type="time", nullable=false)
     *@Assert\NotBlank(message="La hora de inicio no puede estar en blanco.")
     */
    private $horainicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horafin", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora final no puede estar en blanco.")
     */
    private $horafin;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=500, nullable=true)
     */
    private $observacion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="observacion_estatus", type="string", length=500, nullable=true)
     */
    private $observacionEstatus;
    
     /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_productor_id", referencedColumnName="id")
     * })
     *  
     * 
     */
    private $postProductor;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus=1;
    
    /**
     * @var \Estudiocabina.estudiosCabinas
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estudio_cabina_id", referencedColumnName="id")
     * })
     * 
     * @Assert\NotNull(message="Debe seleccionar un estudio o una cabina o una sala para realizar una reservación.")
     * 
     */
    
    private $estudioCabina;

    /**
    *  @ORM\OneToOne(targetEntity="\Frontend\EstudioCabinaBundle\Entity\Protocolo", mappedBy="reservacion")
    *
    **/
    private $protocolo;

    public function __toString()
    {
        return $this->pauta;
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
     * Get protocolo
     *
     * @return integer 
     */
    public function getProtocolo()
    {
        return $this->protocolo;
    }

    /**
     * Set perfilId
     *
     * @param integer $perfilId
     * @return Reservaciones
     */
    public function setPerfilId($perfilId)
    {
        $this->perfilId = $perfilId;
    
        return $this;
    }

    /**
     * Get perfilId
     *
     * @return integer 
     */
    public function getPerfilId()
    {
        return $this->perfilId;
    }

    /**
     * Set pauta
     *
     * @param string $pauta
     * @return Reservaciones
     */
    public function setPauta($pauta)
    {
        $this->pauta = $pauta;
    
        return $this;
    }

    /**
     * Get pauta
     *
     * @return string 
     */
    public function getPauta()
    {
        return $this->pauta;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Reservaciones
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set horainicio
     *
     * @param \DateTime $horainicio
     * @return Reservaciones
     */
    public function setHorainicio($horainicio)
    {
        $this->horainicio = $horainicio;
    
        return $this;
    }

    /**
     * Get horainicio
     *
     * @return \DateTime 
     */
    public function getHorainicio()
    {
        return $this->horainicio;
    }

    /**
     * Set horafin
     *
     * @param \DateTime $horafin
     * @return Reservaciones
     */
    public function setHorafin($horafin)
    {
        $this->horafin = $horafin;
    
        return $this;
    }

    /**
     * Get horafin
     *
     * @return \DateTime 
     */
    public function getHorafin()
    {
        return $this->horafin;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Reservaciones
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set observacionEstatus
     *
     * @param string $observacionEstatus
     * @return Reservaciones
     */
    public function setObservacionEstatus($observacionEstatus)
    {
        $this->observacionEstatus = $observacionEstatus;
    
        return $this;
    }

    /**
     * Get observacionEstatus
     *
     * @return string 
     */
    public function getObservacionEstatus()
    {
        return $this->observacionEstatus;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Reservaciones
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
     * Set postProductor
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $postProductor
     * @return Reservaciones
     */
    public function setPostProductor(\Administracion\UsuarioBundle\Entity\Perfil $postProductor = null)
    {
        $this->postProductor = $postProductor;
    
        return $this;
    }

    /**
     * Get postProductor
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getPostProductor()
    {
        return $this->postProductor;
    }

    /**
     * Set estudioCabina
     *
     * @param \Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas $estudioCabina
     * @return Reservaciones
     */
    public function setEstudioCabina(\Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas $estudioCabina = null)
    {
        $this->estudioCabina = $estudioCabina;
    
        return $this;
    }

    /**
     * Get estudioCabina
     *
     * @return \Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas 
     */
    public function getEstudioCabina()
    {
        return $this->estudioCabina;
    }
}