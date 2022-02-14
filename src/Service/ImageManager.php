<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageManager
{
    private string $avatarDir;
    private ?UploadedFile $image = null;
    private array $errors = [];
    private ?string $filename = null;

    public function __construct(string $avatarDir)
    {
        $this->avatarDir = $avatarDir;
    }

    /**
     * Set the value of image
     * 
     * @param UploadedFile|null $image
     * 
     * @return self
     */
    public function setImage(?UploadedFile $image = null): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Moves a file with a secured name to a new location
     * 
     * @param int $userId
     * 
     * @return self
     */
    public function moveImage(int $userId): self
    {
        if($this->image === null) {
            $this->errors[] = "Il n'y a pas de fichier";
            return $this;
        }
        $this->filename = 'avatar-' . $userId . '.'. $this->image->guessExtension(); 
        $this->deleteImage($this->filename);
        try {
            $this->image->move($this->avatarDir, $this->filename);
        } catch (FileException  $th) {
            $this->errors[] = "Le fichier n'a pas pu être enregistré";
        }
        return $this;
    }

    /**
     * Delete an image if it exists
     * 
     * @param string $filename
     * 
     * @return self
     */
    public function deleteImage(string $filename): self
    {
        $file = $this->avatarDir . DIRECTORY_SEPARATOR . $filename;
        if(file_exists($file)) {
            unlink($file);
        }
        return $this;
    }

    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the value of filename
     * 
     * @return string|null
     */ 
    public function getFilename(): ?string
    {
        return $this->filename;
    }
}