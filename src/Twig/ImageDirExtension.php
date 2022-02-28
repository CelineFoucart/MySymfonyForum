<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ImageDirExtension
 * 
 * ImageDirExtension handles link to image directory.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class ImageDirExtension extends AbstractExtension
{
    private const AVATAR_DIR = '/avatar/';

    public function getFunctions(): array
    {
        return [
            new TwigFunction('avatar_format', [$this, 'getImageTag'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Returns a html img tag with a valid path to a file.
     */
    public function getImageTag(?string $filename = null): string
    {
        $path = self::AVATAR_DIR;
        if (null === $filename) {
            $path .= 'no_avatar.png';
        } else {
            $path .= $filename;
        }

        return '<img src="'.$path.'" alt="avatar de l\'utilisateur">';
    }
}
