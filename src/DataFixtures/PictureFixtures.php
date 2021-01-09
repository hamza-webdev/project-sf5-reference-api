<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Picture;
use App\Services\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
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
    /**
     * @var string juste une copie de folder to-upload car elle sera supprimer apres le move
    */
    private string $filesToUploadDirectoryCopy;
    // on recupere le nom de dossier dans service.yaml, bind $uploadsDirectory, on inject dans construct
    private string $uploadsDirectory;

    private ObjectManager $manager;
    private \Faker\Generator $faker;
    private FileUploader $fileUploader;  
    private Filesystem $filesystem;

    public function __construct(
        FileUploader $fileUploader, 
        Filesystem $filesystem,
        KernelInterface $kernel,
        string $uploadsDirectory
    )
    {
        $this->filesToUploadDirectory = "{$kernel->getProjectDir()}/public/to-upload/";
        $this->filesToUploadDirectoryCopy = "{$kernel->getProjectDir()}/public/to-upload-copy/";
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * Undocumented function
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        //copier un dossier nommé to-upload-copy de origin dossier to-upload (avec tous les images dedans)
        $this->copyToUploadDirectory();

        //supprimer le dossier uploads de public just pour ne pas avoir pleins 
        // de images a chaque relance de load fixtures
        $this->removeExistingUploadDirectoryAndRecreate();
        
        $this->faker = Factory::create();
        $this->generateArticlesPicture(); 

        // apres l execution de script le dossier origin to-load va etre supprimer, alors on rename le dossier 
        // copy (to-load-copy) => to-load et vis versa
        $this->renameToUploadDirectoryCopy();

        $this->manager->flush();
    }

    private function copyToUploadDirectory(): void
    {
        // faire creer le copy dossier de to-upload avec FileSystem
        $this->filesystem->mkdir($this->filesToUploadDirectoryCopy);

        //copie all files dans to-upload dans le new directory copy
        $this->filesystem->mirror($this->filesToUploadDirectory, $this->filesToUploadDirectoryCopy);

    }

    private function renameToUploadDirectoryCopy(): void
    {
        $this->filesystem->rename($this->filesToUploadDirectoryCopy, $this->filesToUploadDirectory);
    }

    private function removeExistingUploadDirectoryAndRecreate(): void
    {
        if($this->filesystem->exists($this->uploadsDirectory))
        {
            $this->filesystem->remove($this->uploadsDirectory);
            $this->filesystem->mkdir($this->uploadsDirectory);
        }
        
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
            if($key === \array_key_last(self::$pictures))
            {
                $this->filesystem->remove($this->filesToUploadDirectory);
               // \rmdir($this->filesToUploadDirectory);
            }

        }

    }
}
