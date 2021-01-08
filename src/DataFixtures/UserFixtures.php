<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private ObjectManager $manager;
    private \Faker\Generator $faker;
    private UserPasswordEncoderInterface $userPasswordEncoderInterface;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
    }

    public function load(ObjectManager $manager): void
    {
         $this->manager = $manager;
        $this->faker = Factory::create();
        $this->generateUsers(2); 
        $this->manager->flush();
    }

    public function generateUsers(int $number): void
    {
        for($i = 0; $i < $number; $i++)
        {
            $user = new User();
            $user->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'badpassword'))      ->setEmail($this->faker->email);
            
            $this->manager->persist($user);
            
        }
    }
}
