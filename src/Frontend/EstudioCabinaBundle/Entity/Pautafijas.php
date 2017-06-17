<?php

namespace Frontend\EstudioCabinaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Estudiocabina.pautafijas
 *
 * @ORM\Table(name="estudiocabina.pautafijas")
 * @ORM\Entity
 */
class Pautafijas
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="estudiocabina.pautafijas_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pauta", type="string", nullable=false)
     * @Assert\NotBlank(message="El nombre de la pauta fija no puede estar en blanco."))
     */
    private $pauta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horainicio", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora de inicio no puede estar en blanco.")
     */
    private $horainicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horafin", type="time", nullable=false)
     * @Assert\NotBlank(message="La hora de fin no puede estar en blanco.")
     */
    private $horafin;

     /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=500, nullable=true)
     */
    private $observacion;
    
    /**
     * @var \Estudiocabina.EstudiosCabinas
     *
     * @ORM\ManyToOne(targetEntity="Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estudio_cabina_id", referencedColumnName="id")
     * })
     * 
     * 
     * @Assert\NotNull(message="Debe seleccionar un estudio o cabina para la pauta fija.")
     */
    
    private $estudioCabina;

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
     * @var string
     *
     * @ORM\Column(name="dias_pautas", type="string", nullable=false)
     * @Assert\NotBlank(message="El día de la pauta no puede estar en blanco."))
     * @Assert\NotNull(message="Debe seleccionar un día para la pauta fija.")
     */
    
    private $diasPautas;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="date", nullable=false)
     */
    private $fechaRegistro;

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pauta
     *
     * @param string $pauta
     * @return Pautafijas
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
     * Set horainicio
     *
     * @param \DateTime $horainicio
     * @return Pautafijas
     */
    public function sethorainicio($horainicio)
    {
        $this->horainicio = $horainicio;
    
        return $this;
    }

    /**
     * Get horainicio
     *
     * @return \DateTime 
     */
    public function gethorainicio()
    {
        return $this->horainicio;
    }

    /**
     * Set horafin
     *
     * @param \DateTime  $horafin
     * @return Pautafijas
     */
    public function sethorafin($horafin)
    {
        $this->horafin = $horafin;
    
        return $this;
    }

    /**
     * Get horafin
     *
     * @return \DateTime  
     */
    public function gethorafin()
    {
        return $this->horafin;
    }
    
     /**
     * Set observacion
     *
     * @param string $observacion
     * @return Pautafijas
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
     * Set postProductor
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $postProductor
     * @return Pautafijas
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
     * @param \Frontend\EstudioCabinaBundle\Entity\Estudiocabina.estudiosCabinas $estudioCabina
     * @return Pautafijas
     */
    public function setEstudioCabina(\Frontend\EstudioCabinaBundle\Entity\estudiosCabinas $estudioCabina = null)
    {
        $this->estudioCabina = $estudioCabina;
    
        return $this;
    }

    /**
     * Get estudioCabina
     *
     * @return \Frontend\EstudioCabinaBundle\Entity\Estudiocabina.estudiosCabinas 
     */
    public function getEstudioCabina()
    {
        return $this->estudioCabina;
    }
    
    /**
     * Set diasPautas
     *
     * @param string $diasPautas
     * @return Pautafijas
     */
    public function setDiasPautas($diasPautas)
    {
        $this->diasPautas = $diasPautas;
    
        return $this;
    }

    /**
     * Get diasPautas
     *
     * @return string 
     */
    public function getDiasPautas()
    {
        return $this->diasPautas;
    }
    
    /**
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     * @return Reservaciones
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }
}