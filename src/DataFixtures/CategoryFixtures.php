<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{

    private ObjectManager $manager;
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->generateCategories(3);

        $this->manager->flush();
    }

    public function generateCategories(int $number): void
    {
        for ($i=0; $i <= $number; $i++) { 
            $category = new Category();
            $category->setName("Category {$i}");

            $this->addReference("category{$i}", $category);
            $this->manager->persist($category);
        }
    }
}
