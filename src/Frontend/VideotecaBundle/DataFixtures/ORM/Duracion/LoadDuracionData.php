<?php 

namespace Frontend\VideotecaBundle\DataFixtures\ORM\Duracion;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\Duracion;

class LoadDuracionData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $duraciones = array(
            array('nombre' => '64 min.'),
            array('nombre' => '94 min.'),
            array('nombre' => '126 min.'),
            array('nombre' => 'S/D'),
            array('nombre' => '186 min.'),
            array('nombre' => '66 min.'),
            array('nombre' => '33 min.'),
            array('nombre' => '12 min.'),
        );

        foreach ($duraciones as $duracion) {
            $entity = new Duracion();
            $entity->setNombre($duracion['nombre']);

            $manager->persist($entity);
            $manager->flush();
        }

    }
}