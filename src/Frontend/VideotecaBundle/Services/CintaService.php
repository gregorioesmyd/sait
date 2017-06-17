<?php

namespace Frontend\VideotecaBundle\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Description of CintaService
 *
 * @author ecastro
 */
class CintaService {

    private $em;
    private $cinta;
    private $slug;
    private $ultimoCodigo;
    private $nuevoCodigo;
    private $codigoCinta;

    public function __construct($em, $ultimoCodigo) {
        $this->em = $em;
        $this->ultimoCodigo = $ultimoCodigo;
    }

    private function getUltimoCodigo(){
    	$options = $this->getOptions();
    	$this->ultimoCodigo = $this->em->getRepository('VideotecaBundle:UltimoCodigo')
                                	->findOneBy($options);

    	$this->nuevoCodigo = $this->ultimoCodigo->getUltimoCodigo() + 1;
    }

    private function setUltimoCodigo()
    {
    	switch ($this->slug) {
    		case 'prensa':
				$this->setCodigoPrensa();
    			break;
            case 'videoteca':
                $this->setCodigoVideoteca();
                break;
            case 'deporte':
                $this->setCodigoDeporte();
                break;
            case 'satelite':
                $this->setCodigoSatelite();
                break;
    	}
    }

    private function getOptions()
    {
    	if ($this->slug == "deporte" || $this->slug == "satelite") {
    		$options = array('formato' => $this->cinta->getFormato()->getId(), 'categoria' => $this->cinta->getCategoria()->getId());
    	} else {
    		$options = array(
                'formato' => $this->cinta->getFormato(),
                'categoria' => $this->em->getRepository('VideotecaBundle:Categoria')
                                        ->findOneByTipoCinta($this->cinta->getTipoCinta()));
    	}
    	return $options;
    }

    private function getTipoCinta()
    {
    	$tipoCinta = $this->em->getRepository('VideotecaBundle:TipoCinta')
                            	->findOneBySlug($this->slug);
    	return $tipoCinta;
    }

    public function save($cinta, $slug, $user)
    {
    	$this->cinta = $cinta;
    	$this->slug  = $slug;
    	$this->user  = $user;

    	$this->cinta->setTipoCinta($this->getTipoCinta());
        $this->getUltimoCodigo();
        $this->setUltimoCodigo();
        $this->codigoCinta = $this->ultimoCodigo->getCategoria()->getCorrelativo() . '-' . $this->ultimoCodigo->getUltimoCodigo();
        $this->cinta->setCodigo($this->codigoCinta);
        $this->cinta->setCategoria($this->ultimoCodigo->getCategoria());
        $this->cinta->setStatus($this->em->getRepository('VideotecaBundle:Status')->findOneByNombre('Disponible'));
        $this->setPropiedad();
        $this->em->persist($this->cinta);
        $this->em->flush();
    }

    public function setPropiedad()
    {
        if (!$this->cinta->getPropiedad()) 
        {
            $this->cinta->setPropiedad($this->em->getRepository('VideotecaBundle:Propiedad')->findOneByNombre('N/A'));
        }
    }

    public function setCodigoPrensa()
    {
    	$formato = $this->cinta->getFormato();
        $dvcam = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCam');
        $dvcpro = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro');
        $dvcpro94l = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro 94L');
        $categoria = $this->ultimoCodigo->getCategoria();
    	if ($formato->getId() == $dvcam->getId() || $formato->getId() == $dvcpro->getId() || $formato->getId() == $dvcpro94l->getId()) {
            $query = $query = $this->em->createQuery('SELECT u FROM VideotecaBundle:UltimoCodigo u WHERE u.categoria= :categoria AND u.formato IN (:format1, :format2, :format3)');
            $query->setParameter('categoria', $categoria->getId());
            $query->setParameter('format1', $dvcam->getId());
            $query->setParameter('format2', $dvcpro->getId());
            $query->setParameter('format3', $dvcpro94l->getId());
            $ultimosCodigos = $query->getResult();
            foreach ($ultimosCodigos as $ultimoCodigo) {
                $ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
                $this->em->persist($ultimoCodigo);
            }
    	} else {
    		$this->ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
    		$this->em->persist($this->ultimoCodigo);
    	}
    }

    public function setCodigoVideoteca()
    {
        $formato = $this->cinta->getFormato();
        $dvcam = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCam');
        $dvcpro = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro');
        $dvcpro94l = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro 33M');
        $categoria = $this->ultimoCodigo->getCategoria();
        if ($formato->getId() == $dvcam->getId() || $formato->getId() == $dvcpro->getId() || $formato->getId() == $dvcpro94l->getId()) {
            $query = $query = $this->em->createQuery('SELECT u FROM VideotecaBundle:UltimoCodigo u WHERE u.categoria= :categoria AND u.formato IN (:format1, :format2, :format3)');
            $query->setParameter('categoria', $categoria->getId());
            $query->setParameter('format1', $dvcam->getId());
            $query->setParameter('format2', $dvcpro->getId());
            $query->setParameter('format3', $dvcpro94l->getId());
            $ultimosCodigos = $query->getResult();
            foreach ($ultimosCodigos as $ultimoCodigo) {
                $ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
                $this->em->persist($ultimoCodigo);
            }
        } else {
            $this->ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
            $this->em->persist($this->ultimoCodigo);
        }
    }

    public function setCodigoDeporte()
    {
        $formato = $this->cinta->getFormato();
        $dvcam = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCam');
        $dvcpro = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro');
        $dvcpro94l = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro 94L');
        $categoria = $this->ultimoCodigo->getCategoria();
        if ($formato->getId() == $dvcam->getId() || $formato->getId() == $dvcpro->getId() || $formato->getId() == $dvcpro94l->getId()) {
            $query = $query = $this->em->createQuery('SELECT u FROM VideotecaBundle:UltimoCodigo u WHERE u.categoria= :categoria AND u.formato IN (:format1, :format2, :format3)');
            $query->setParameter('categoria', $categoria->getId());
            $query->setParameter('format1', $dvcam->getId());
            $query->setParameter('format2', $dvcpro->getId());
            $query->setParameter('format3', $dvcpro94l->getId());
            $ultimosCodigos = $query->getResult();
            foreach ($ultimosCodigos as $ultimoCodigo) {
                $ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
                $this->em->persist($ultimoCodigo);
            }
        } else {
            $this->ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
            $this->em->persist($this->ultimoCodigo);
        }
    }

    public function setCodigoSatelite()
    {
        $formato = $this->cinta->getFormato();
        $dvcam = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCam');
        $dvcpro = $this->em->getRepository('VideotecaBundle:Formato')->findOneByDescripcionFormato('DVCPro');
        $categoria = $this->ultimoCodigo->getCategoria();
        if ($formato->getId() == $dvcam->getId() || $formato->getId() == $dvcpro->getId()) {
            $query = $query = $this->em->createQuery('SELECT u FROM VideotecaBundle:UltimoCodigo u WHERE u.categoria= :categoria AND u.formato IN (:format1, :format2)');
            $query->setParameter('categoria', $categoria->getId());
            $query->setParameter('format1', $dvcam->getId());
            $query->setParameter('format2', $dvcpro->getId());
            $ultimosCodigos = $query->getResult();
            foreach ($ultimosCodigos as $ultimoCodigo) {
                $ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
                $this->em->persist($ultimoCodigo);
            }
        } else {
            $this->ultimoCodigo->setUltimoCodigo($this->nuevoCodigo);
            $this->em->persist($this->ultimoCodigo);
        }
    }
}
