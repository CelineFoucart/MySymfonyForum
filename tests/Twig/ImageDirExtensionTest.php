<?php

namespace App\Tests\Twig;

use App\Twig\ImageDirExtension;
use PHPUnit\Framework\TestCase;

class ImageDirExtensionTest extends TestCase
{
    private ImageDirExtension $imageDir;

    protected function setUp(): void
    {
        $this->imageDir = new ImageDirExtension();
    }
    
    public function testWithNoFile(): void
    {
        $img = $this->imageDir->getImageTag();
        $expected = '<img src="/avatar/no_avatar.png" alt="avatar de l\'utilisateur">';
        $this->assertEquals($expected, $img);
    }

    public function testWithFile(): void
    {
        $img = $this->imageDir->getImageTag('avatar-1.png');
        $expected = '<img src="/avatar/avatar-1.png" alt="avatar de l\'utilisateur">';
        $this->assertEquals($expected, $img);
    }
}
