<?php

namespace App\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyleStack;

class OutputFormatterStyles
{
    public static function getStyles(): array
    {
        return [
            'head' => new OutputFormatterStyle('yellow', '', []),
            'info' => new OutputFormatterStyle('white', '', []),
            'dark' => new OutputFormatterStyle('gray', '', []),
            'done' => new OutputFormatterStyle('green', '', []),
            'skip' => new OutputFormatterStyle('blue', '', []),
            'fail' => new OutputFormatterStyle('red', '', []),
        ];
    }
}
