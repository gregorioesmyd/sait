<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsables
 *
 * @ORM\Table(name="controlequipo.operaciones")
 * @ORM\Entity
 */
class Operaciones
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="controlequipo.operaciones_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \responsableUsuario
     *
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="operaciones")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *   }
     * )
     */
    private $reponsableUsuario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_operacion", type="datetime", nullable=false)
     */
    private $fechaOperacion;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_operacion", type="integer", nullable=false)
     */
    private $tipoOperacion;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set reponsableUsuario
     *
     * @param integer $reponsableUsuario
     * @return Operaciones
     */
    public function setReponsableUsuario(\Administracion\UsuarioBundle\Entity\Perfil $reponsableUsuario = null)
    {
        $this->reponsableUsuario = $reponsableUsuario;
    
        return $this;
    }

    /**
     * Get reponsableUsuario
     *
     * @return integer 
     */
    public function getReponsableUsuario()
    {
        return $this->reponsableUsuario;
    }

    /**
     * Set fechaOperacion
     *
     * @param \DateTime $fechaOperacion
     * @return Operaciones
     */
    public function setFechaOperacion($fechaOperacion)
    {
        $this->fechaOperacion = $fechaOperacion;
    
        return $this;
    }

    /**
     * Get fechaOperacion
     *
     * @return \DateTime 
     */
    public function getFechaOperacion()
    {
        return $this->fechaOperacion;
    }

    /**
     * Get tipoOperacion
     *
     * @return \Frontend\ControlEquipoBundle\Entity\TiposOperaciones 
     */
    public function getTipoOperacion()
    {
        return $this->tipoOperacion;
    }

    /**
     * Set tipoOperacion
     *
     * @return \integer
     */
    public function setTipoOperacion($tipoOperacion)
    {
        $this->tipoOperacion = $tipoOperacion;
        return $this;
    }
    /**
     * Set pauta
     *
     * @param \Frontend\ControlEquipoBundle\Entity\Pautas $pauta
     * @return Responsables
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
}