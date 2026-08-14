<?php

declare(strict_types=1);

namespace Nex\Core;

use InvalidArgumentException;
use Nex\Exceptions\ServiceNotFoundException;

/**
 * Lightweight dependency injection container.
 *
 * The Nex container is intentionally simple. It acts as a registry of factory
 * callbacks capable of producing services on demand.
 *
 * Supported lifetimes:
 *
 * - Transient  : Creates a new instance on every request.
 * - Singleton  : Creates one shared instance.
 * - Multiton   : Creates one shared instance per identifier.
 */
final class Container
{
    /**
     * Always create a new instance.
     */
    public const TRANSIENT = 'transient';

    /**
     * Create a single shared instance.
     */
    public const SINGLETON = 'singleton';

    /**
     * Create one shared instance per identifier.
     */
    public const MULTITON = 'multiton';

    /**
     * Internal singleton instance identifier.
     */
    private const SINGLETON_ID = 0;

    /**
     * Registered service bindings.
     *
     * @var array<
     *     string,
     *     array<
     *         string,
     *         array{
     *             callback: callable,
     *             mode: string,
     *             instances: array<int|string,mixed>
     *         }
     *     >
     * >
     */
    private array $bindings = [];

    /**
     * Register a service.
     *
     * @param string              $fqn
     * @param callable(Container):mixed $callback
     * @param string              $mode
     * @param string              $profile
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public function bind(
        string $fqn,
        callable $callback,
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        $this->assertMode($mode);

        $this->bindings[$fqn][$profile] = [
            'callback'  => $callback,
            'mode'      => $mode,
            'instances' => [],
        ];

        return $this;
    }

    /**
     * Register a factory callback.
     *
     * @param callable(Container):mixed $callback
     */
    public function bindFactory(
        string $fqn,
        callable $callback,
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind($fqn, $callback, $mode, $profile);
    }

    /**
     * Register an object factory.
     */
    public function bindFactoryObject(
        string $fqn,
        object $factory,
        string $method = 'create',
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $fqn,
            [$factory, $method],
            $mode,
            $profile
        );
    }

    /**
     * Register a static factory.
     */
    public function bindStaticFactory(
        string $fqn,
        string $factoryClass,
        string $method = 'create',
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $fqn,
            [$factoryClass, $method],
            $mode,
            $profile
        );
    }

    /**
     * Register a factory resolved from the container.
     */
    public function bindFactoryService(
        string $fqn,
        string $factoryService,
        string $method = 'create',
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $fqn,
            [$this->make($factoryService), $method],
            $mode,
            $profile
        );
    }

    /**
     * Register an invokable factory object.
     */
    public function bindInvokableFactory(
        string $fqn,
        object $factory,
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind($fqn, $factory, $mode, $profile);
    }

    /**
     * Register a prototype object.
     *
     * A cloned instance is returned every time the service is resolved.
     */
    public function bindPrototype(
        string $fqn,
        object $prototype,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $fqn,
            static fn () => clone $prototype,
            self::TRANSIENT,
            $profile
        );
    }

    /**
     * Register a fixed value.
     */
    public function bindValue(
        string $fqn,
        mixed $value,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $fqn,
            static fn () => $value,
            self::SINGLETON,
            $profile
        );
    }

    /**
     * Register an alias.
     */
    public function bindAlias(
        string $alias,
        string $target,
        string $mode = self::TRANSIENT,
        string $profile = 'main'
    ): static {
        return $this->bind(
            $alias,
            fn (Container $c) => $c->make($target),
            $mode,
            $profile
        );
    }

    /**
     * Register a singleton.
     */
    public function bindSingleton(
        string $fqn,
        callable $callback,
        string $profile = 'main'
    ): static {
        return $this->bind($fqn, $callback, self::SINGLETON, $profile);
    }

    /**
     * Register a multiton.
     */
    public function bindMultiton(
        string $fqn,
        callable $callback,
        string $profile = 'main'
    ): static {
        return $this->bind($fqn, $callback, self::MULTITON, $profile);
    }

    /**
     * Register an existing object instance.
     */
    public function instance(
        string $fqn,
        object $instance,
        string $profile = 'main'
    ): static {
        $this->bindings[$fqn][$profile] = [
            'callback' => static fn () => $instance,
            'mode' => self::SINGLETON,
            'instances' => [
                self::SINGLETON_ID => $instance,
            ],
        ];

        return $this;
    }

    /**
     * Determine whether a service is registered.
     */
    public function has(
        string $fqn,
        string $profile = 'main'
    ): bool {
        return isset($this->bindings[$fqn][$profile]);
    }

    /**
     * Remove a service registration.
     */
    public function forget(
        string $fqn,
        string $profile = 'main'
    ): static {
        unset($this->bindings[$fqn][$profile]);

        return $this;
    }

    /**
     * Remove a cached service instance.
     */
    public function forgetInstance(
        string $fqn,
        int|string $id = self::SINGLETON_ID,
        string $profile = 'main'
    ): static {
        unset($this->bindings[$fqn][$profile]['instances'][$id]);

        return $this;
    }

    /**
     * Remove every registered service.
     */
    public function flush(): static
    {
        $this->bindings = [];

        return $this;
    }

    /**
     * Remove all cached instances while keeping registrations.
     */
    public function flushInstances(): static
    {
        foreach ($this->bindings as &$profiles) {
            foreach ($profiles as &$binding) {
                $binding['instances'] = [];
            }
        }

        unset($binding, $profiles);

        return $this;
    }

    /**
     * Resolve a service.
     *
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function make(
        string $fqn,
        int|string $id = self::SINGLETON_ID,
        string $profile = 'main'
    ): mixed {
        if (!isset($this->bindings[$fqn][$profile])) {
            throw new ServiceNotFoundException($fqn);
        }

        $binding = &$this->bindings[$fqn][$profile];

        return match ($binding['mode']) {
            self::TRANSIENT => ($binding['callback'])($this),

            self::SINGLETON => $this->resolveSingleton($binding),

            self::MULTITON => $this->resolveMultiton($binding, $id),

            default => throw new InvalidArgumentException(
                "Unsupported container mode '{$binding['mode']}'."
            ),
        };
    }

    /**
     * Resolve a singleton instance.
     *
     * @param array{
     *     callback: callable,
     *     mode: string,
     *     instances: array<int|string,mixed>
     * } $binding
     */
    private function resolveSingleton(array &$binding): mixed
    {
        if (!isset($binding['instances'][self::SINGLETON_ID])) {
            $binding['instances'][self::SINGLETON_ID] =
                ($binding['callback'])($this);
        }

        return $binding['instances'][self::SINGLETON_ID];
    }

    /**
     * Resolve a multiton instance.
     *
     * @param array{
     *     callback: callable,
     *     mode: string,
     *     instances: array<int|string,mixed>
     * } $binding
     */
    private function resolveMultiton(
        array &$binding,
        int|string $id
    ): mixed {
        if (!isset($binding['instances'][$id])) {
            $binding['instances'][$id] =
                ($binding['callback'])($this);
        }

        return $binding['instances'][$id];
    }

    /**
     * Validate a binding mode.
     *
     * @throws InvalidArgumentException
     */
    private function assertMode(string $mode): void
    {
        if (!in_array($mode, [
            self::TRANSIENT,
            self::SINGLETON,
            self::MULTITON,
        ], true)) {
            throw new InvalidArgumentException(
                "Unsupported container mode '{$mode}'."
            );
        }
    }
}