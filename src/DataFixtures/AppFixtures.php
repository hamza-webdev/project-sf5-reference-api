<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private $faker;
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->generateArticles(5); 
        $this->manager->flush();
    }

    private function generateArticles(int $number): void
    {
        for($i = 0; $i < $number; $i++)
        {
            $article = new Article();

            [
                "dataObject" => $dataObject,
                "dateString" => $dateString
            ] = $this->generateRandomDateBetweenRange('01/01/2021', '01/05/2021');

            $article->setTitle($this->faker->sentence(4))
                    ->setContent($this->faker->paragraph())
                    ->setSlug("article-{$i}-{$dateString}")
                    ->setCreatedAt($dataObject)
                    ->setIsPublished(false);

            $this->manager->persist($article);
        }
    }

    private function generateRandomDateBetweenRange(string $start, string $end): array
    {
        //format date fr DD/MM/YY
        $startDateTimestamp = (\DateTime::createFromFormat('d/m/Y', $start))->getTimestamp();
        $endDateTimestamp = (\DateTime::createFromFormat('d/m/Y', $end))->getTimestamp();

        $randomTimestamp = \mt_rand($startDateTimestamp, $endDateTimestamp);
        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);

        return [
            "dataObject" => $dateTimeImmutable,
            "dateString" => $dateTimeImmutable->format('d-m-Y')
        ];

    }

}
