<?php

declare(strict_types=1);

namespace Nex\Exceptions;

use \RuntimeException;

final class GlobalNotFoundException extends RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(
            sprintf("Global '%s' is not registered.", $name)
        );
    }
}