<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testType()
    {
        $t = new Type('foo');

        $this->assertSame('foo', $t->value());
        $this->assertSame('foo', (string) $t);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyType()
    {
        new Type('');
    }
}
