<?php

namespace Frontend\ControlEquipoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsables
 *
 * @ORM\Table(name="controlequipo.operaciones_externos")
 * @ORM\Entity
 */
class OperacionesExternos
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
     * @ORM\ManyToOne(targetEntity="Administracion\UsuarioBundle\Entity\Perfil", inversedBy="operaciones_externos")
     * @ORM\JoinTable(name="usuarios.perfil",
     *   joinColumns={
     *     @ORM\JoinColumn(name="responsable_usuario_id", referencedColumnName="id")
     *   }
     * )
     */
    private $reponsableUsuario;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_operacion", type="integer", nullable=false)
     */
    private $tipoOperacion;

    /**
     * @var \Pautas
     *
     * @ORM\ManyToOne(targetEntity="RegistrosExternos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registro_equipo_externo_id", referencedColumnName="id")
     * })
     */
    private $registroEquipoExterno;



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
     * Set registroEquipoExterno
     *
     * @param \Frontend\ControlEquipoBundle\Entity\RegistrosExternos $registroEquipoExterno
     * @return Responsables
     */
    public function setRegistroEquipoExterno(\Frontend\ControlEquipoBundle\Entity\RegistrosExternos $registroEquipoExterno = null)
    {
        $this->registroEquipoExterno = $registroEquipoExterno;
    
        return $this;
    }

    /**
     * Get registroEquipoExterno
     *
     * @return \Frontend\ControlEquipoBundle\Entity\RegistrosExternos 
     */
    public function getRegistroEquipoExterno()
    {
        return $this->registroEquipoExterno;
    }
}