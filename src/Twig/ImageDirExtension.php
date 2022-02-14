<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageDirExtension extends AbstractExtension
{
    private const AVATAR_DIR = '/avatar/';

    public function getFunctions(): array
    {
        return [
            new TwigFunction('avatar_format', [$this, 'getImageTag'], ['is_safe' => ['html']]),
        ];
    }

    public function getImageTag(?string $filename = null)
    {
        $path = self::AVATAR_DIR;
        if($filename === null) {
            $path .= 'no_avatar.png';
        } else {
            $path .= $filename;
        }
        return '<img src="'.$path.'" alt="avatar de l\'utilisateur">';
    }
}
