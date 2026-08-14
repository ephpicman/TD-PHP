<?php

declare(strict_types=1);

namespace Nex\Utilities;

final class Str 
{
    public static function length(string $string): int
    {
        return strlen($string);
    }
}