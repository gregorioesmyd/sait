<?php

namespace Frontend\VideotecaBundle\DataFixtures\ORM\Categoria;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\Categoria;

/**
* 
*/
class LoadCategoriaData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc} 
	 */
	public function load(ObjectManager $manager)
	{
		$categorias = array(
			array('nombre' => 'Deportes Satelites', 'tipoCinta' => 'deportes'),
			array('nombre' => 'Deportes Noticiarios', 'tipoCinta' => 'deportes'),
			array('nombre' => 'Deportes Eventos', 'tipoCinta' => 'deportes'),
			array('nombre' => 'Deportes', 'tipoCinta' => 'deportes'),
			array('nombre' => 'Aptn', 'tipoCinta' => 'satelite'),
			array('nombre' => 'Reuters', 'tipoCinta' => 'satelite'),
			array('nombre' => 'Eventos especiales', 'tipoCinta' => 'satelite'),
			array('nombre' => 'Corresponsalias', 'tipoCinta' => 'satelite'),
		);

		foreach ($categorias as $categoria) {
			$objCategoria = new Categoria();
			$objCategoria->setNombre($categoria['nombre']);
	        $objCategoria->setTipoCinta($this->getReference($categoria['tipoCinta']));

	        $manager->persist($objCategoria);
	        $manager->flush();
		}
		
	}

	/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}