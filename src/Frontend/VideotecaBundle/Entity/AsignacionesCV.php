<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AsignacionesCV
 *
 * @ORM\Table(name="videoteca.asignacionCV")
 * @ORM\Entity
 */
class AsignacionesCV
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="videoteca.asignacionescv_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \CintasVirgenes.idCintasVirgenes
     *@ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\CintasVirgenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cintas_virgenes", referencedColumnName="id")
     * })
     * 
     * 
     */
    private $idCintasVirgenes;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id_solicitante", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank(message="Debe de seleccionar un usuario al cual se le va a asignar la cinta."))
     */
    private $idUsuarioSolicitante;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id_prestamista", referencedColumnName="id", nullable=false)
     * })
     * 
     * 
     */
    private $idUsuarioPrestamista;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer")
     * 
     * @Assert\NotBlank(message="Debe indicar la cantida a asignar."))
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="string", length=255)
     * 
     * @Assert\NotBlank(message="Debe indicar motivo por el cual se va a asignar."))
     */
    private $motivo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime")
     */
    private $fechaEntrega;


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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return AsignacionesCV
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return AsignacionesCV
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    
        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     * @return AsignacionesCV
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;
    
        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime 
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set idCintasVirgenes
     *
     * @param \Frontend\VideotecaBundle\Entity\CintasVirgenes $idCintasVirgenes
     * @return AsignacionesCV
     */
    public function setIdCintasVirgenes(\Frontend\VideotecaBundle\Entity\CintasVirgenes $idCintasVirgenes)
    {
        $this->idCintasVirgenes = $idCintasVirgenes;
    
        return $this;
    }

    /**
     * Get idCintasVirgenes
     *
     * @return \Frontend\VideotecaBundle\Entity\CintasVirgenes 
     */
    public function getIdCintasVirgenes()
    {
        return $this->idCintasVirgenes;
    }

    /**
     * Set idUsuarioSolicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $idUsuarioSolicitante
     * @return AsignacionesCV
     */
    public function setIdUsuarioSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $idUsuarioSolicitante)
    {
        $this->idUsuarioSolicitante = $idUsuarioSolicitante;
    
        return $this;
    }

    /**
     * Get idUsuarioSolicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getIdUsuarioSolicitante()
    {
        return $this->idUsuarioSolicitante;
    }

    /**
     * Set idUsuarioPrestamista
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $idUsuarioPrestamista
     * @return AsignacionesCV
     */
    public function setIdUsuarioPrestamista(\Administracion\UsuarioBundle\Entity\Perfil $idUsuarioPrestamista)
    {
        $this->idUsuarioPrestamista = $idUsuarioPrestamista;
    
        return $this;
    }

    /**
     * Get idUsuarioPrestamista
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil 
     */
    public function getIdUsuarioPrestamista()
    {
        return $this->idUsuarioPrestamista;
    }
}