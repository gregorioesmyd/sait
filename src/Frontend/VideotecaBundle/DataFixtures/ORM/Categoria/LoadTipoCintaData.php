<?php

namespace Frontend\VideotecaBundle\DataFixtures\ORM\Categoria;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\TipoCinta;

/**
* 
*/
class LoadTipoCintaData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc} 
	 */
	public function load(ObjectManager $manager)
	{
		$entidad = new TipoCinta();
        $entidad->setNombre("Deportes");
        $manager->persist($entidad);
        $manager->flush();
        $this->addReference('deportes', $entidad);

        $entidad = new TipoCinta();
        $entidad->setNombre("Satélites");
        $manager->persist($entidad);
        $manager->flush();
        $this->addReference('satelite', $entidad);

        $entidad = new TipoCinta();
        $entidad->setNombre("Prensa");
        $manager->persist($entidad);
        $manager->flush();
        $this->addReference('prensa', $entidad);

        $entidad = new TipoCinta();
        $entidad->setNombre("Videoteca");
        $manager->persist($entidad);
        $manager->flush();
        $this->addReference('videoteca', $entidad);
		
	}

	/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}