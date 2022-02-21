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
            'bbcode' => 'url', 
            'htmlOpen' => 'a', 
            'html' => 'a'
        ],
        [
            'bbcode' => 'b', 
            'htmlOpen' => 'strong',
            'html' => 'strong'
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
        ]
    ];

    public function parse(string $text): string
    {
        foreach (self::BBCODE as $value) {
            $parsed = str_replace('[' . $value['bbcode'] . ']', '<'. $value['htmlOpen'] .'>', $text);
            $parsed = str_replace('[/' . $value['bbcode'] . ']', '</'. $value['htmlClose'] .'>', $text);
        }
        return $parsed;
    }
}