<?php

declare(strict_types=1);

namespace Nex\Utilities;

/**
 * BooleanHelper
 *
 * A high-performance utility class providing advanced boolean manipulation,
 * type-safe conversion, and functional logic gates.
 *
 * @package Nex\Utilities
 * @author Sina Kuhestani
 */
final class BooleanHelper
{
    /**
     * List of string values that are evaluated as false in a non-strict context.
     */
    private const FALSY_VALUES = [
        'false',
        '0',
        'no',
        'off',
        'n',
        'f',
        '',
        'null',
        'undefined',
    ];

    /**
     * Prevents instantiation of this utility class.
     * @codeCoverageIgnore
     */
    private function __construct() {}

    /**
     * Converts a mixed value into a boolean based on Nex's truthiness rules.
     * Handles strings, numbers, and booleans with semantic intelligence.
     *
     * @param mixed $value The value to convert.
     * @return bool The resulting boolean value.
     */
    public static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            return !in_array($normalized, self::FALSY_VALUES, true);
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return (bool) $value;
    }

    /**
     * Checks if a value is considered "truthy" according to Nex standards.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isTruthy(mixed $value): bool
    {
        return self::toBool($value) === true;
    }

    /**
     * Checks if a value is considered "falsy" according to Nex standards.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isFalsy(mixed $value): bool
    {
        return !self::isTruthy($value);
    }

    /**
     * Returns the logical negation of the given boolean.
     *
     * @param bool $value
     * @return bool
     */
    public static function not(bool $value): bool
    {
        return !$value;
    }

    /**
     * Logical AND: Returns true if all provided values are true.
     *
     * @param bool $value The initial value.
     * @param bool ...$values Additional values to evaluate.
     * @return bool
     */
    public static function and(bool $value, bool ...$values): bool
    {
        foreach ($values as $v) {
            if (!$v) {
                return false;
            }
        }
        return $value;
    }

    /**
     * Logical OR: Returns true if at least one of the provided values is true.
     *
     * @param bool $value
     * @param bool ...$values
     * @return bool
     */
    public static function or(bool $value, bool ...$values): bool
    {
        if ($value) {
            return true;
        }
        foreach ($values as $v) {
            if ($v) {
                return true;
            }
        }
        return false;
    }

    /**
     * Logical NAND: The negation of AND. Returns false only if all values are true.
     *
     * @param bool $value
     * @param bool ...$values
     * @return bool
     */
    public static function nand(bool $value, bool ...$values): bool
    {
        return !self::and($value, ...$values);
    }

    /**
     * Logical NOR: The negation of OR. Returns true only if all values are false.
     *
     * @param bool $value
     * @param bool ...$values
     * @return bool
     */
    public static function nor(bool $value, bool ...$values): bool
    {
        return !self::or($value, ...$values);
    }

    /**
     * Logical XOR: Returns true if an odd number of operands are true.
     * In a multi-operand context, this follows the parity rule.
     *
     * @param bool $value
     * @param bool ...$values
     * @return bool
     */
    public static function xor(bool $value, bool ...$values): bool
    {
        $allValues = array_merge([$value], $values);
        $trueCount = count(array_filter($allValues, fn(bool $v) => $v));

        return ($trueCount % 2) !== 0;
    }

    /**
     * Logical NXOR (Exclusive NOR): The negation of XOR.
     * Returns true if an even number of operands are true.
     *
     * @param bool $value
     * @param bool ...$values
     * @return bool
     */
    public static function nxor(bool $value, bool ...$values): bool
    {
        return !self::xor($value, ...$values);
    }
}
