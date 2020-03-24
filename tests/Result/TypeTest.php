<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\{
    Result\Type,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testType()
    {
        $type = new Type('foo');

        $this->assertSame('foo', $type->value());
    }

    public function testThrowWhenEmptyType()
    {
        $this->expectException(DomainException::class);

        new Type('');
    }
}
