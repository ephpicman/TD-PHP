<?php

/* -----------------------------------------------------------------------------
 | This file is part of the Nex Framework.
 |-----------------------------------------------------------------------------
 | Copyright (c) 2026 EphpicMan <sinakuhestani@gmail.com>
 |
 | For the full copyright and license information, please see the LICENSE file
 | distributed with this source code.
 |-----------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Nex\Core;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Nex\Exceptions\GlobalNotFoundException;
use Traversable;

/**
 * Runtime application registry.
 *
 * The Globals registry provides a lightweight, mutable key/value store for
 * sharing runtime objects and values throughout the application's lifecycle.
 *
 * Unlike configuration repositories, this registry is intended only for
 * ephemeral runtime state that exists while the application is executing.
 *
 * Typical use cases include:
 *
 * - Current request and response objects
 * - Authenticated user
 * - Runtime flags
 * - Shared context
 * - Lazily initialized objects
 *
 * This class should be registered as a singleton within the Nex dependency
 * injection container.
 *
 * @implements ArrayAccess<string, mixed>
 * @implements IteratorAggregate<string, mixed>
 */
final class Globals implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Stored runtime values.
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Store a runtime value.
     *
     * Existing values are silently overwritten.
     *
     * @param string $name  Registry key.
     * @param mixed  $value Value to store.
     *
     * @throws InvalidArgumentException If the registry key is invalid.
     *
     * @return static
     */
    public function set(string $name, mixed $value): static
    {
        $this->data[$this->normalizeKey($name)] = $value;

        return $this;
    }

    /**
     * Store multiple runtime values.
     *
     * Existing values having the same keys will be overwritten.
     *
     * @param array<string, mixed> $values Values indexed by registry keys.
     *
     * @return static
     */
    public function setMany(array $values): static
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Replace the entire registry.
     *
     * Every previously stored value will be discarded.
     *
     * @param array<string, mixed> $values New registry contents.
     *
     * @return static
     */
    public function replace(array $values): static
    {
        $this->clear();

        return $this->setMany($values);
    }

    /**
     * Retrieve a runtime value.
     *
     * If the specified key does not exist, the supplied default value is
     * returned instead.
     *
     * @param string $name
     * @param mixed  $default Default value returned when the key is missing.
     *
     * @return mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->has($name) ? $this->data[$name] : $default;
    }

    /**
     * Retrieve a runtime value or throw an exception.
     *
     * Use this method when the requested value is mandatory for the current
     * operation.
     *
     * @param string $name Registry key.
     *
     * @throws GlobalNotFoundException If the requested key does not exist.
     *
     * @return mixed
     */
    public function getOrFail(string $name): mixed
    {
        if (!$this->has($name)) {
            throw new GlobalNotFoundException($name);
        }

        return $this->data[$name];
    }

    /**
     * Determine whether the specified registry key exists.
     *
     * Unlike isset(), this method correctly reports keys whose value is null.
     *
     * @param string $name Registry key.
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->data);
    }

    /**
     * Remove a runtime value.
     *
     * Calling this method for a non-existent key has no effect.
     *
     * @param string $name Registry key.
     *
     * @return static
     */
    public function remove(string $name): static
    {
        unset($this->data[$name]);

        return $this;
    }

    /**
     * Retrieve and remove a runtime value.
     *
     * If the key does not exist, the supplied default value is returned.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function pull(string $name, mixed $default = null): mixed
    {
        if (!$this->has($name)) {
            return $default;
        }

        $value = $this->data[$name];

        unset($this->data[$name]);

        return $value;
    }

    /**
     * Lazily store a runtime value.
     *
     * The resolver is executed only when the specified key has not already
     * been registered.
     *
     * @param string          $name
     * @param callable():mixed $resolver
     *
     * @return mixed
     */
    public function remember(string $name, callable $resolver): mixed
    {
        if (!$this->has($name)) {
            $this->data[$name] = $resolver();
        }

        return $this->data[$name];
    }

    /**
     * Remove every registered value.
     *
     * @return static
     */
    public function clear(): static
    {
        $this->data = [];

        return $this;
    }

    /**
     * Retrieve every registered value.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Determine whether the registry contains any values.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    /**
     * Count registered values.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->data);
    }

    /**
     * Retrieve an iterator for all registered values.
     *
     * Enables native foreach() iteration.
     *
     * @return Traversable<string, mixed>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Retrieve a value using property syntax.
     *
     * Example:
     *
     * $globals->request
     *
     * @param string $name Registry key.
     *
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->getOrFail($name);
    }

    /**
     * Store a value using property syntax.
     *
     * Example:
     *
     * $globals->request = $request;
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Determine whether a property exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Remove a property.
     *
     * @param string $name
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    /**
     * Determine whether an array offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($this->normalizeOffset($offset));
    }

    /**
     * Retrieve a value using array syntax.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->getOrFail($this->normalizeOffset($offset));
    }

    /**
     * Store a value using array syntax.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($this->normalizeOffset($offset), $value);
    }

    /**
     * Remove a value using array syntax.
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($this->normalizeOffset($offset));
    }

    /**
     * Normalize and validate an ArrayAccess offset.
     *
     * @param mixed $offset
     *
     * @throws InvalidArgumentException If the offset is invalid.
     *
     * @return string
     */
    private function normalizeOffset(mixed $offset): string
    {
        if (!is_string($offset) || $offset === '') {
            throw new InvalidArgumentException(
                'Global registry keys must be non-empty strings.'
            );
        }

        return $this->normalizeKey($offset);
    }

    /**
     * Normalize and validate a registry key.
     *
     * Registry keys must be non-empty strings.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException If the key is invalid.
     *
     * @return string
     */
    private function normalizeKey(string $name): string
    {
        if ('' === $name) {
            throw new InvalidArgumentException(
                'Global registry keys cannot be empty.'
            );
        }

        return $name;
    }
}
