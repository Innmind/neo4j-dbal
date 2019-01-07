<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    public function testInterface()
    {
        $parameter = new Parameter('foo', ['value']);
        $this->assertSame('foo', $parameter->key());
        $this->assertSame(['value'], $parameter->value());
    }

    public function testThrowWhenEmptyKey()
    {
        $this->expectException(DomainException::class);

        new Parameter('', 'foo');
    }
}
