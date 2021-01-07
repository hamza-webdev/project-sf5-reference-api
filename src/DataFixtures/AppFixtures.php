<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private \Faker\Generator $faker;
    private SluggerInterface $slugger;

    /**
     * Constructor
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;        
    }

    /**
     * Load Fixture
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->generateArticles(5); 
        $this->manager->flush();
    }

    /**
     * Generate Article
     * @param int $number
     */
    private function generateArticles(int $number): void
    {
        for($i = 0; $i < $number; $i++)
        {
            $article = new Article();

            [
                "dateObject" => $dateObject,
                "dateString" => $dateString
            ] = $this->generateRandomDateBetweenRange('01/01/2021', '01/05/2021');

            $title = $this->faker->sentence(4);
            $slug = $this->slugger->slug(\strtolower($title).'-' .$dateString );

            $article->setTitle($title)
                    ->setContent($this->faker->paragraph())
                    ->setSlug($slug)
                    ->setCreatedAt($dateObject)
                    ->setIsPublished(false);

            $this->manager->persist($article);
        }
    }

    /**
     * Generate Date DatetimesStamp object
     * @param string $start Date string with format 'd/m/Y'
     * @param string $end Date string with format 'd/m/Y'
     *
     * @return array{dateObject: \DateTimeImmutable, dateString: string} String with "d-m-Y"
     */
    private function generateRandomDateBetweenRange(string $start, string $end): array
    {

        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate = \DateTime::createFromFormat('d/m/Y', $end);
        if(!$startDate || !$endDate){
            throw new HttpException(400, "La date saisie doit etre sous format d/m/Y");            
        }
        //format date fr DD/MM/YY
        $startDateTimestamp = ($startDate)->getTimestamp();
        $endDateTimestamp = ($endDate)->getTimestamp();

        $randomTimestamp = \mt_rand($startDateTimestamp, $endDateTimestamp);
        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);

        return [
            "dateObject" => $dateTimeImmutable,
            "dateString" => $dateTimeImmutable->format('d-m-Y')
        ];

    }

}
