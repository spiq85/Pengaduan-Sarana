<?php

namespace App\Services\Comment;

class CommentModerationService
{
    private const BLOCKED_WORDS = [
        'anjing',
        'babi',
        'monyet',
        'bangsat',
        'kontol',
        'memek',
        'tolol',
        'goblok',
        'bego',
        'idiot',
        'tai',
        'ngentot',
        'ngewe',
        'nigga',
        'nigger',
        'kntl',
        'ngtd',
        'mmk',
        'ngentod',
        'anj',
        'bajingan',
        'jawa',
        'hitam',
        'ireng',
        'mnyt',
        'autis',
    ];

    public static function censor(string $text): string
    {
        $pattern = '/\\b(' . implode('|', array_map('preg_quote', self::BLOCKED_WORDS)) . ')\\b/iu';

        return preg_replace_callback($pattern, function ($match) {
            return str_repeat('*', mb_strlen($match[0]));
        }, $text) ?? $text;
    }
}
