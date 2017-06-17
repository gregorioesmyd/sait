<?php

namespace Frontend\VideotecaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="videoteca.ultimocodigo")
 */
class UltimoCodigo
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private $id;

    /**
     * @ORM\Column(name="ultimocodigo", type="integer")
     */
    private $ultimocodigo;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Formato", inversedBy="ultimoCodigos")
     * @ORM\JoinColumn(name="formato_id", referencedColumnName="id")
     */
    private $formato;

    /**
     * @ORM\ManyToOne(targetEntity="Frontend\VideotecaBundle\Entity\Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;

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
     * Set ultimocodigo
     *
     * @param integer $ultimocodigo
     * @return UltimoCodigo
     */
    public function setUltimocodigo($ultimocodigo)
    {
        $this->ultimocodigo = $ultimocodigo;

        return $this;
    }

    /**
     * Get ultimocodigo
     *
     * @return integer
     */
    public function getUltimocodigo()
    {
        return $this->ultimocodigo;
    }


    /**
     * Set formato
     *
     * @param \Frontend\VideotecaBundle\Entity\Formato $formato
     * @return UltimoCodigo
     */
    public function setFormato(\Frontend\VideotecaBundle\Entity\Formato $formato = null)
    {
        $this->formato = $formato;

        return $this;
    }

    /**
     * Get formato
     *
     * @return \Frontend\VideotecaBundle\Entity\Formato
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Set categoria
     *
     * @param \Frontend\VideotecaBundle\Entity\Categoria $categoria
     * @return UltimoCodigo
     */
    public function setCategoria(\Frontend\VideotecaBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Frontend\VideotecaBundle\Entity\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
}