<?php

namespace Solicitudes\SolicitudesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Solicitudes.solicitudes
 *
 * @ORM\Table(name="solicitudes.solicitudes")
 * @ORM\Entity
 */
class Solicitudes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="solicitudes.solicitudes_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=70, nullable=false)
     * @Assert\NotNull(message="El campo nombre no debe estar vacio.")
     */
    private $nombre;

    /**
     * @var text
     *
     * @ORM\Column(name="problema", type="text", nullable=false)
     * @Assert\NotNull(message="El campo problema no debe estar vacio.")
     */
    private $problema;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiarios", type="string", length=255, nullable=false)
     * @Assert\NotNull(message="El campo beneficiario no debe estar vacio.")
     */
    private $beneficiarios;
     

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitante_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $solicitante;

    /**
     * @var \Estatus
     * @ORM\ManyToOne(targetEntity="Solicitudes\SolicitudesBundle\Entity\Estatus")
     * @ORM\JoinColumn(name="estatus_id", referencedColumnName="id", nullable=false)
     */
    private $estatus;

    /**
     * @var \Requerimientos
     * @ORM\OneToMany(targetEntity="Solicitudes\SolicitudesBundle\Entity\Requerimientos", mappedBy="solicitudes", cascade={"persist", "remove"})
     **/

    private $requerimientos;

    /**
     * @var \Responsables
     * @ORM\OneToMany(targetEntity="Solicitudes\SolicitudesBundle\Entity\Responsables", mappedBy="solicitudes", cascade={"persist", "remove"})
     **/
    private $responsables;


    /**
     * @var \Unidades
     * @ORM\OneToMany(targetEntity="Solicitudes\SolicitudesBundle\Entity\Unidades", mappedBy="solicitudes", cascade={"persist", "remove"})
     **/
    private $unidades;


    /**
     * @var string
     *
     */
    public function __construct() {
        $this->requerimientos = new ArrayCollection();
        $this->responsables = new ArrayCollection();
        $this->unidades = new ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Solicitudes
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }


    /**
     * Set problema
     *
     * @param string $problema
     * @return Solicitudes
     */
    public function setProblema($problema)
    {
        $this->problema = $problema;
    
        return $this;
    }

    /**
     * Get problema
     *
     * @return string 
     */
    public function getProblema()
    {
        return $this->problema;
    }

    /**
     * Set beneficiarios
     *
     * @param string $beneficiarios
     * @return Solicitudes
     */
    public function setBeneficiarios($beneficiarios)
    {
        $this->beneficiarios = $beneficiarios;
    
        return $this;
    }

    /**
     * Get beneficiarios
     *
     * @return string 
     */
    public function getBeneficiarios()
    {
        return $this->beneficiarios;
    }

  
     /**
     * Set solicitante
     *
     * @param \Administracion\UsuarioBundle\Entity\Perfil $solicitante
     * @return Perfil
     */
    public function setSolicitante(\Administracion\UsuarioBundle\Entity\Perfil $solicitante = null)
    {
        $this->solicitante = $solicitante;

        return $this;
    }

    /**
     * Get solicitante
     *
     * @return \Administracion\UsuarioBundle\Entity\Perfil
     */
    public function getSolicitante()
    {
        return $this->solicitante;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     * @return Operador
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
     * Add requerimientos
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Requerimientos $requerimientos
     * @return Solicitudes
     */
    public function addRequerimiento(\Solicitudes\SolicitudesBundle\Entity\Requerimientos $requerimientos)
    {
        $this->requerimientos[] = $requerimientos;
    
        return $this;
    }

    /**
     * Remove requerimientos
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Requerimientos $requerimientos
     */
    public function removeRequerimiento(\Solicitudes\SolicitudesBundle\Entity\Requerimientos $requerimientos)
    {
        $this->requerimientos->removeElement($requerimientos);
    }

    /**
     * Get requerimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequerimientos()
    {
        return $this->requerimientos;
    }

    /**
     * Add responsables
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Responsables $responsables
     * @return Solicitudes
     */
    public function addResponsable(\Solicitudes\SolicitudesBundle\Entity\Responsables $responsables)
    {
        $this->responsables[] = $responsables;
    
        return $this;
    }

    /**
     * Remove responsables
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Responsables $responsables
     */
    public function removeResponsable(\Solicitudes\SolicitudesBundle\Entity\Responsables $responsables)
    {
        $this->responsables->removeElement($responsables);
    }

    /**
     * Get responsables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponsables()
    {
        return $this->responsables;
    }

    /**
     * Add unidades
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Unidades $unidades
     * @return Solicitudes
     */
    public function addUnidad(\Solicitudes\SolicitudesBundle\Entity\Unidades $unidades)
    {
        $this->unidades[] = $unidades;
    
        return $this;
    }

    /**
     * Remove unidades
     *
     * @param \Solicitudes\SolicitudesBundle\Entity\Unidades $unidades
     */
    public function removeUnidad(\Solicitudes\SolicitudesBundle\Entity\Unidades $unidades)
    {
        $this->unidades->removeElement($unidades);
    }

    /**
     * Get unidades
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnidades()
    {
        return $this->unidades;
    }
}