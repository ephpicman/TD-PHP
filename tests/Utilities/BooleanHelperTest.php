<?php

declare(strict_types=1);

namespace Nex\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Nex\Utilities\BooleanHelper;

/**
 * BooleanHelperTest
 */
final class BooleanHelperTest extends TestCase
{
    /**
     * @dataProvider toBoolProvider
     */
    public function testToBool(mixed $input, bool $expected): void
    {
        $this->assertSame($expected, BooleanHelper::toBool($input));
    }

    public static function toBoolProvider(): array
    {
        return [
            // Booleans
            'bool true' => [true, true],
            'bool false' => [false, false],

            // Strings (Truthy)
            'string truthy' => ['true', true],
            'string truthy text' => ['hello', true],
            'string truthy spaces' => ['  yes  ', true],

            // Strings (Falsy)
            'string falsy false' => ['false', false],
            'string falsy no' => ['no', false],
            'string falsy off' => ['off', false],
            'string falsy n' => ['n', false],
            'string falsy f' => ['f', false],
            'string falsy empty' => ['', false],
            'string falsy null' => ['null', false],
            'string falsy undefined' => ['undefined', false],
            'string falsy zero' => ['0', false],

            // Numerics
            'numeric 1' => [1, true],
            'numeric 0' => [0, false],
            'numeric -1' => [-1, true],

            // Other types (casting to bool)
            'array non-empty' => [[1], true],
            'array empty' => [[], false],
            'object' => [new \stdClass(), true],
        ];
    }

    public function testIsTruthy(): void
    {
        $this->assertTrue(BooleanHelper::isTruthy('yes'));
        $this->assertTrue(BooleanHelper::isTruthy(true));
        $this->assertFalse(BooleanHelper::isTruthy('false'));
        $this->assertFalse(BooleanHelper::isTruthy(0));
    }

    public function testNot(): void
    {
        $this->assertTrue(BooleanHelper::not(false));
        $this->assertFalse(BooleanHelper::not(true));
    }

    public function testAnd(): void
    {
        $this->assertTrue(BooleanHelper::and(true, true, true));
        $this->assertFalse(BooleanHelper::and(true, false, true));
        $this->assertFalse(BooleanHelper::and(false, true));
    }

    public function testOr(): void
    {
        $this->assertTrue(BooleanHelper::or(true, false, false));
        $this->assertTrue(BooleanHelper::or(false, true, false));
        $this->assertFalse(BooleanHelper::or(false, false, false));
    }

    public function testNand(): void
    {
        $this->assertFalse(BooleanHelper::nand(true, true, true));
        $this->assertTrue(BooleanHelper::nand(true, false, true));
    }

    public function testNor(): void
    {
        $this->assertTrue(BooleanHelper::nor(false, false, false));
        $this->assertFalse(BooleanHelper::nor(true, false));
    }

    public function testXor(): void
    {
        // Odd number of true values
        $this->assertTrue(BooleanHelper::xor(true, false, false));
        $this->assertTrue(BooleanHelper::xor(true, true, true));

        // Even number of true values
        $this->assertFalse(BooleanHelper::xor(true, true, false));
        $this->assertFalse(BooleanHelper::xor(false, false, false));
    }

    public function testNxor(): void
    {
        // Negation of XOR
        $this->assertFalse(BooleanHelper::nxor(true, false, false));
        $this->assertTrue(BooleanHelper::nxor(true, true, false));
    }
}
