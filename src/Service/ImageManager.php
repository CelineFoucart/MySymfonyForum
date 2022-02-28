<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageManager
 * 
 * ImageManager handles image uploading.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
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
     * Set the value of image.
     */
    public function setImage(?UploadedFile $image = null): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Moves a file with a secured name to a new location.
     */
    public function moveImage(int $userId): self
    {
        if (null === $this->image) {
            $this->errors[] = "Il n'y a pas de fichier";

            return $this;
        }
        $this->filename = 'avatar-'.$userId.'.'.$this->image->guessExtension();
        $this->deleteImage($this->filename);
        try {
            $this->image->move($this->avatarDir, $this->filename);
        } catch (FileException  $th) {
            $this->errors[] = "Le fichier n'a pas pu être enregistré";
        }

        return $this;
    }

    /**
     * Delete an image if it exists.
     */
    public function deleteImage(string $filename): self
    {
        $file = $this->avatarDir.DIRECTORY_SEPARATOR.$filename;
        if (file_exists($file)) {
            unlink($file);
        }

        return $this;
    }

    /**
     * Get the value of errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the value of filename.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }
}
