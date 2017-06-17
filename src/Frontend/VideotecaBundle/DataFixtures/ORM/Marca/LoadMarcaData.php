<?php 

namespace Frontend\VideotecaBundle\DataFixtures\ORM\Marca;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\Marca;

class LoadMarcaData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $marcas = array(
            array('nombre' => 'DTK'),
            array('nombre' => 'Fuji'),
            array('nombre' => 'Genérico'),
            array('nombre' => 'Maxell'),
            array('nombre' => 'Panasonic'),
            array('nombre' => 'Sony'),
        );

        foreach ($marcas as $marca) {
            $entity = new Marca();
            $entity->setDescripcionMarca($marca['nombre']);

            $manager->persist($entity);
            $manager->flush();
        }

    }
}