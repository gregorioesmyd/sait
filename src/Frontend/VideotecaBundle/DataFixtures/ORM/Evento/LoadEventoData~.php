<?php

namespace Frontend\VideotecaBundle\DoctrineFixtures\ORM\Evento;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\Evento;

class LoadEventoData implements FixtureInterface
{
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$eventos = array(
			array('nombre' => 'Air Master'),
            array('nombre' => 'Copia'),
            array('nombre' => 'Master Original'),
            array('nombre' => 'Master'),
            array('nombre' => 'Satelite'),
            array('nombre' => 'Material en Bruto'),
            array('nombre' => 'Master Copia'),
		);

		foreach ($eventos as $evento) {
			$entity = new Evento();
			$entity->setNombre($evento['nombre']);
			$manager->persist($entity);
			$manager->flush();
		}
	}

}