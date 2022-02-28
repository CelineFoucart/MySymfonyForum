<?php

namespace App\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class InformationWebsiteExtension
 * 
 * InformationWebsiteExtension handles website informations for a twig view.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class InformationWebsiteExtension extends AbstractExtension
{
    /**
     * @var string website title
     */
    private string $websiteName;

    /**
     * @var string description for meta description tag
     */
    private string $websiteDescription;

    public function __construct(string $websiteName, string $websiteDescription)
    {
        $this->websiteName = $websiteName;
        $this->websiteDescription = $websiteDescription;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('website_name', [$this, 'getWebsiteName']),
            new TwigFunction('website_description', [$this, 'getWebsiteDescription']),
            new TwigFunction('current_date', [$this, 'getCurrentDate']),
        ];
    }

    /**
     * Get the value of websiteName.
     */
    public function getWebsiteName(): string
    {
        return $this->websiteName;
    }

    /**
     * Get the value of websiteDescription.
     */
    public function getWebsiteDescription(): string
    {
        return $this->websiteDescription;
    }

    /**
     * Get current date formated as 'd/m/Y, H:i'.
     */
    public function getCurrentDate(): string
    {
        return (new DateTime())->format('d/m/Y, H:i');
    }
}
