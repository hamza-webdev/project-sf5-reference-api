<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class FileUploader
{
    private SluggerInterface $slugger;
    private string $uploadsDirectory;
    /**
     * 
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @param string $uploadsDirectory
     */
    public function __construct(SluggerInterface $slugger, string $uploadsDirectory)
    {
        $this->slugger = $slugger;
        $this->uploadsDirectory = $uploadsDirectory;

    }

    /**
     * Upload file and return filename and filepath
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return array<string>
     */
    public function upload(UploadedFile $file): array
    {
        $fileName = $this->generateUniqueFileName($file);

        try {
            $file->move($this->uploadsDirectory, $fileName);
        } catch (FileException $e) {
            throw $e;
        }
        return [
            'fileName' => $fileName,
            'filePath' => $this->uploadsDirectory . $fileName
        ];

    }

    /**
     * Generate a unique file for the uploadeed file
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return string
     */
    private function generateUniqueFileName(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalFileNameSlugged = $this->slugger->slug(strtolower($originalName));
        $randomId = uniqId();

        return "{$originalFileNameSlugged}-{$randomId}.{$file->guessExtension()}";
    }


}