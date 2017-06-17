<?php 

namespace Frontend\VideotecaBundle\DataFixtures\ORM\Formato;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\VideotecaBundle\Entity\Formato;

class LoadFormatoData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $formatos = array(
            array('nombre' => 'Betacam'),
            array('nombre' => 'DVC-R'),
            array('nombre' => 'DVCam'),
            array('nombre' => 'DVCPro'),
            array('nombre' => 'DVD'),
            array('nombre' => 'MiniDV'),
            array('nombre' => 'SuperVHS'),
            array('nombre' => 'VHS 70000'),
            array('nombre' => 'DVCPro 33M'),
            array('nombre' => 'DVCpro 94L'),
        );

        foreach ($formatos as $formato) {
            $entity = new Formato();
            $entity->setDescripcionFormato($formato['nombre']);

            $manager->persist($entity);
            $manager->flush();
        }

    }
}