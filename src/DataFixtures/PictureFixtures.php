<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Picture;
use App\Services\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureFixtures extends Fixture
{
    /**
     * @var Array<string>
     */
    private static array $pictures = [
        '7.jpg',
        '3.png',
        '4.png',
        '6.png',
        '8.png',
        '9.png',
        '2.jpg',
        '22.jpg'
    ];
    private string $filesToUploadDirectory;
    private ObjectManager $manager;
    private \Faker\Generator $faker;
    private FileUploader $fileUploader;
    // private SluggerInterface $slugger;

    public function __construct(FileUploader $fileUploader, KernelInterface $kernel)
    {
        $this->filesToUploadDirectory = "{$kernel->getProjectDir()}/public/to-upload/";
        $this->fileUploader = $fileUploader;
    }

    /**
     * Undocumented function
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        
        $this->faker = Factory::create();
        $this->generateArticlesPicture(); 

        $this->manager->flush();
    }

    /**
     * Undocumented function
     */
    private function generateArticlesPicture(): void
    {

        foreach(self::$pictures as $key => $pictureFile)
        {
            $picture = new Picture();
           
            [
                'fileName' => $pictureName,
                'filePath' => $picturePath
            ] = $this->fileUploader->upload(new UploadedFile($this->filesToUploadDirectory . $pictureFile, $pictureFile, null, null, true ));

            $picture->setPictureName($pictureName)
                    ->setPicturePath($picturePath);

            $this->addReference("picture{$key}", $picture);
            $this->manager->persist($picture);

            // delete directory to-upload in public dir
            // if($key === \array_key_last(self::$pictures))
            // {
            //    // \rmdir($this->filesToUploadDirectory);
            // }

        }

    }
}
