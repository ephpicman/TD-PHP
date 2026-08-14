<?php

declare(strict_types=1);

namespace Nex\Base;

use Nex\Core\Container;
use Nex\Core\WP;

abstract class Hook
{
    /**
     * WordPress hook name.
     */
    protected static string $hook = '';

    /**
     * Hook priority.
     */
    protected static int $priority = 10;

    /**
     * Number of accepted arguments.
     */
    protected static int $acceptedArgs = 1;

    /**
     * Whether this hook is an action or filter.
     */
    protected static bool $filter = false;

    /**
     * Register the hook.
     */
    public static function register(Container $container): void
    {
        $callback = static function (mixed ...$arguments) use ($container): mixed {
            return $container
                ->make(static::class)
                ->handle(...$arguments);
        };

        if (static::$filter) {
            WP::addFilter(
                static::$hook,
                $callback,
                static::$priority,
                static::$acceptedArgs
            );

            return;
        }

        WP::addAction(
            static::$hook,
            $callback,
            static::$priority,
            static::$acceptedArgs
        );
    }

    /**
     * Execute the hook.
     */
    abstract public function handle(mixed ...$arguments): mixed;
}