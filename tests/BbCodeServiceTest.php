<?php

namespace App\Tests;

use App\Service\BbCodeService;
use PHPUnit\Framework\TestCase;

class BbCodeServiceTest extends TestCase
{
    protected BbCodeService $parser;

    protected function setUp(): void
    {
        $this->parser = new BbCodeService();
    }

    public function testParseBoldText(): void
    {
        $expected = 'lorem ipsum <strong>sit</strong> amet consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [b]sit[/b] amet consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseItalicText(): void
    {
        $expected = 'lorem ipsum <em>sit</em> amet consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [i]sit[/i] amet consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseUnderlinedText(): void
    {
        $expected = 'lorem ipsum <span style="text-decoration:underline">sit</span> amet consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [u]sit[/u] amet consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseQuoteText(): void
    {
        $expected = 'lorem ipsum <blockquote>sit</blockquote> amet consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [quote]sit[/quote] amet consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseWithMultipleTags(): void
    {
        $expected = 'lorem ipsum <blockquote>sit <em>sit</em> consequuntur.</blockquote> Lorem ipsum <strong>sit</strong> amet consequuntur.';
        $text = 'lorem ipsum [quote]sit [i]sit[/i] consequuntur.[/quote] Lorem ipsum [b]sit[/b] amet consequuntur.';
        $parsed = $this->parser->parse($text);
        $this->assertEquals($expected, $parsed);
    }

    public function testParseQuoteTextWithAuthor(): void
    {
        $expected = 'lorem ipsum <blockquote><cite>Ermina a écrit :</cite>sit</blockquote> amet consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [quote=Ermina]sit[/quote] amet consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseUnorderedListText(): void
    {
        $expected = 'lorem ipsum <ul><li>sit</li><li>amet</li></ul> consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [ul][li]sit[/li][li]amet[/li][/ul] consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseOrderedListText(): void
    {
        $expected = 'lorem ipsum <ol><li>sit</li><li>amet</li></ol> consequuntur.';
        $parsed = $this->parser->parse('lorem ipsum [ol][li]sit[/li][li]amet[/li][/ol] consequuntur.');
        $this->assertEquals($expected, $parsed);
    }

    public function testParseSpoilerText(): void
    {
        $expected = '<div class="spoiler" data-controller="spoiler"><div class="spoiler-top" data-action="click->spoiler#toggle">Spoiler: <span data-spoiler-target="action">montrer</span></div><div class="hide" data-spoiler-target="content">sit</div></div>';
        $parsed = $this->parser->parse('[spoiler]sit[/spoiler]');
        $this->assertEquals($expected, $parsed);
    }
}
