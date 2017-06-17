<?php

namespace Frontend\TruequesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Frontend\TruequesBundle\Entity\Producto;
use Frontend\TruequesBundle\Entity\Categoria;

class LoadProductData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

    	$foods = array(
			array('nombre' => 'Aceite', 'categoria' => 1),
			array('nombre' => 'Atún', 'categoria' => 1),
			array('nombre' => 'Azúcar', 'categoria' => 1),
			array('nombre' => 'Café', 'categoria' => 1),
			array('nombre' => 'Fórmula infantil', 'categoria' => 1),
			array('nombre' => 'Harina de maíz', 'categoria' => 1),
			array('nombre' => 'Harina de trigo', 'categoria' => 1),
			array('nombre' => 'Huevos', 'categoria' => 1),
			array('nombre' => 'Leche en polvo', 'categoria' => 1),
			array('nombre' => 'Mantequilla', 'categoria' => 1),
			array('nombre' => 'Mayonesa', 'categoria' => 1),
			array('nombre' => 'Pasta', 'categoria' => 1),
			array('nombre' => 'Sal', 'categoria' => 1),
			array('nombre' => 'Salsa de tomate', 'categoria' => 1),
			array('nombre' => 'Sardina', 'categoria' => 1),
		);

		$cleanliness = array(
			array('nombre' => 'Acetona', 'categoria' => 2),
			array('nombre' => 'Acondicionador', 'categoria' => 2),
			array('nombre' => 'Afeitadora', 'categoria' => 2),
			array('nombre' => 'Champú', 'categoria' => 2),
			array('nombre' => 'Crema corporal', 'categoria' => 2),
			array('nombre' => 'Crema dental', 'categoria' => 2),
			array('nombre' => 'Desodorante', 'categoria' => 2),
			array('nombre' => 'Enjuague bucal', 'categoria' => 2),
			array('nombre' => 'Hilo dental', 'categoria' => 2),
			array('nombre' => 'Jabón de baño', 'categoria' => 2),
			array('nombre' => 'Toallas húmedas', 'categoria' => 2),
			array('nombre' => 'Toallas sanitarias', 'categoria' => 2),
			array('nombre' => 'Toallas diarias', 'categoria' => 2),
			array('nombre' => 'Pañales de niño', 'categoria' => 2),
			array('nombre' => 'Pañales de adulto', 'categoria' => 2),
			array('nombre' => 'Gelatina para el cabello', 'categoria' => 2),
		);

		$cleanup = array(
			array('nombre' => 'Jabón en polvo', 'categoria' => 3),
			array('nombre' => 'Jabón en barra', 'categoria' => 3),
			array('nombre' => 'Cloro', 'categoria' => 3),
			array('nombre' => 'Suavizante', 'categoria' => 3),
			array('nombre' => 'Desinfectante', 'categoria' => 3),
			array('nombre' => 'Lavaplatos', 'categoria' => 3),
			array('nombre' => 'Insecticida', 'categoria' => 3),
			array('nombre' => 'Ambientador', 'categoria' => 3),
		);

		$category = new Categoria();
		$category->setNombre('Alimentos');
		$manager->persist($category);

		foreach ($foods as $food) {
			$entidad = new Producto();
			$entidad->setNombre($food['nombre']);
			$entidad->setCategoria($category);
			$manager->persist($entidad);
		}
        $manager->flush();

        $category = new Categoria();
		$category->setNombre('Aseo Personal');
		$manager->persist($category);

        foreach ($cleanliness as $cleanline) {
			$entidad = new Producto();
			$entidad->setNombre($cleanline['nombre']);
			$entidad->setCategoria($category);
			$manager->persist($entidad);
		}
        $manager->flush();

        $category = new Categoria();
		$category->setNombre('Limpieza');
		$manager->persist($category);

        foreach ($cleanup as $clean) {
			$entidad = new Producto();
			$entidad->setNombre($clean['nombre']);
			$entidad->setCategoria($category);
			$manager->persist($entidad);
		}
        $manager->flush();

        $category = new Categoria();
		$category->setNombre('Medicina');
		$manager->persist($category);
		$manager->flush();

    }
}