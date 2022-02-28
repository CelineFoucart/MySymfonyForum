<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ActiveLinkExtension
 * 
 * ActiveLinkExtension handles active links.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class ActiveLinkExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_link', [$this, 'isActive']),
        ];
    }

    /**
     * Determines if a link is active.
     */
    public function isActive(string $currentUrl, string $navbarUrl, bool $strict = true): string
    {
        if ($strict) {
            $isActive = $currentUrl === $navbarUrl;
        } else {
            $isActive = $navbarUrl === substr($currentUrl, 0, strlen($navbarUrl));
        }

        return ($isActive) ? 'active' : '';
    }
}
