<?php

namespace Frontend\NominaBundle\Entity\Reporte;
use Symfony\Component\Validator\Constraints as Assert;

class Historico
{
    
    /**
     * @Assert\NotBlank(message="Debe escribir año desde.")
     */
    private $aniodesde;
    
    /**
     * @Assert\NotBlank(message="Debe escribir año hasta.")
     */
    private $aniohasta;

    /**
     * @Assert\NotBlank(message="Debe escribir mes desde.")
     */
    private $mesdesde;
    
    /**
     * @Assert\NotBlank(message="Debe escribir mes hasta.")
     */
    private $meshasta;
    
    /**
     * @Assert\NotBlank(message="Debe escribir la cedula.")
     * @Assert\Type(type="digit", message="Este valor debe ser numérico")
     */
    private $cedula;

    
    public function setAniodesde($aniodesde)
    {
        $this->aniodesde = $aniodesde;
    
        return $this;
    }

    public function getAniodesde()
    {
        return $this->aniodesde;
    }
    
    public function setMesdesde($mesdesde)
    {
        $this->mesdesde = $mesdesde;
    
        return $this;
    }

    public function getMesdesde()
    {
        return $this->mesdesde;
    }


    public function setAniohasta($aniohasta)
    {
        $this->aniohasta = $aniohasta;
    
        return $this;
    }

    public function getAniohasta()
    {
        return $this->aniohasta;
    }
    
    
     public function setMeshasta($meshasta)
    {
        $this->meshasta = $meshasta;
    
        return $this;
    }

    public function getMeshasta()
    {
        return $this->meshasta;
    }   

    
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    
        return $this;
    }

    public function getCedula()
    {
        return $this->cedula;
    }

}

