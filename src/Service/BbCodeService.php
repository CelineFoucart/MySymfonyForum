<?php

namespace App\Service;

class BbCodeService
{
    const BBCODE = [
        [
            'bbcode' => 'quote', 
            'htmlOpen' => 'blockquote',
            'htmlClose' => 'blockquote'
        ],
        [
            'bbcode' => 'b', 
            'htmlOpen' => 'strong',
            'htmlClose' => 'strong'
        ],
        [
            'bbcode' => 'i', 
            'htmlOpen' => 'em', 
            'htmlClose' => 'em'
        ],
        [
            'bbcode' => 'u', 
            'htmlOpen' => 'span style="text-decoration:underline"', 
            'htmlClose' => 'span'
        ],
        [
            'bbcode' => 'ul', 
            'htmlOpen' => 'ul', 
            'htmlClose' => 'ul'
        ],
        [
            'bbcode' => 'ol', 
            'htmlOpen' => 'ol', 
            'htmlClose' => 'ol'
        ],
        [
            'bbcode' => 'li', 
            'htmlOpen' => 'li', 
            'htmlClose' => 'li'
        ]
    ];
    
    public function parse(string $text): string
    {
        $text = preg_replace('#\[quote=(.+)\](.+)\[/quote\]#isU', '<blockquote><cite>$1 a écrit :</cite>$2</blockquote>', $text);
        foreach (self::BBCODE as $value) {
            $text = str_replace('[' . $value['bbcode'] . ']', '<'. $value['htmlOpen'] .'>', $text);
            $text = str_replace('[/' . $value['bbcode'] . ']', '</'. $value['htmlClose'] .'>', $text);
        }

        $text = str_replace('[spoiler]', '<div class="spoiler" data-controller="spoiler"><div class="spoiler-top" data-action="click->spoiler#toggle">Spoiler: <span data-spoiler-target="action">montrer</span></div><div class="hide" data-spoiler-target="content">', $text);
        $text = str_replace('[/spoiler]', '</div></div>', $text);

        $text = preg_replace('#\[url\](.+)\[\/url\]#iUs', '<a href="$1">$1</a>', $text);
        $text = preg_replace('#\[url\=(.+)\](.+)\[\/url\]#iUs', '<a href="$1">$2</a>', $text);
        $text = nl2br($text);
        $text = str_replace('blockquote><br />', 'blockquote>', $text);
        $text = str_replace('l><br />', 'l>', $text);
        return $text;
    }
}