<?php

declare(strict_types=1);

namespace Nex\Exceptions;

use \RuntimeException;

/**
 * Thrown when the dependency injection container cannot resolve a service.
 */
final class ServiceNotFoundException extends RuntimeException
{
    /**
     * Create a new service not found exception.
     *
     * @param string $service Fully-qualified service identifier.
     */
    public function __construct(string $service)
    {
        parent::__construct(
            sprintf(
                "Service '%s' is not registered in the container.",
                $service
            )
        );
    }
}