<?php

namespace App\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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
            new TwigFunction('current_date', [$this, 'getCurrentDate'])
        ];
    }

    public function getWebsiteName(): string
    {
        return $this->websiteName;
    }

    public function getWebsiteDescription(): string
    {
        return $this->websiteDescription;
    }

    public function getCurrentDate(): string
    {
        return (new DateTime())->format("dd/mm/YY, H:i:s");
    }
}
