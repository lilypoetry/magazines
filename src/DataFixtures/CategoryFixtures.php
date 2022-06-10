<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 15; $i++) {

            $category = new Category();
            $category->setName($faker->company);
            $category->setColor($faker->hexcolor);

            // Obligatoirement ajouter une reference pour pouvoir rilier category_id au magazine
            $this->addReference("category_$i", $category);

            // Puis aujouter "implements DependentFixtureInterface" au MagazineFixtures

            $manager->persist($category);
        }

        $manager->flush();
    }
}
