<?php

namespace App\Tests\Twig;

use App\Twig\InformationWebsiteExtension;
use PHPUnit\Framework\TestCase;

class InformationWebsiteExtensionTest extends TestCase
{
    private InformationWebsiteExtension $extension;

    private array $data = [
        "name" => "Mon site",
        "description" => "description SEO de mon super site"
    ];

    protected function setUp(): void
    {
        $this->extension = new InformationWebsiteExtension($this->data['name'], $this->data['description']);
    }
    
    public function testName(): void
    {
        $this->assertEquals($this->data['name'], $this->extension->getWebsiteName());
    }

    public function testDescription(): void
    {
        $this->assertEquals($this->data['description'], $this->extension->getWebsiteDescription());
    }
}
