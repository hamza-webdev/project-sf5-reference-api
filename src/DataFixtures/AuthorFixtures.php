<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Author;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AuthorFixtures extends Fixture
{
    private ObjectManager $manager;
    private \Faker\Generator $faker;
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->generateAuthors(2);

        $this->manager->flush();
    }

    public function generateAuthors(int $number): void
    {
        for ($i=0; $i < $number; $i++) { 
            $author = new Author();
            $author->setName($this->faker->name());

            $this->addReference("author{$i}", $author);
            $this->manager->persist($author);
        }
    }
}
