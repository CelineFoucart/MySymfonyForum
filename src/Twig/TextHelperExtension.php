<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextHelperExtension extends AbstractExtension
{
    protected const MAX_LENGTH = 200;

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'], ['is_safe' => ['html']]),
            new TwigFilter('format_text', [$this, 'formatText'], ['is_safe' => ['html']])
        ];
    }

    public function excerpt(string $text): string
    {
        if ($text <= 200) {
            return $text;
        }
        $excerpt = mb_substr($text, 0, self::MAX_LENGTH);
        $last = mb_strripos($excerpt, ' ');
        return mb_substr($excerpt, 0, $last) . '[...]';
    }

    public function formatText(string $text): string
    {
        return nl2br($text);
    }
}
