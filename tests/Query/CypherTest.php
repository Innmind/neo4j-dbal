<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query\Cypher,
    Query,
    Query\Parameter,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class CypherTest extends TestCase
{
    public function testInterface()
    {
        $cypher = new Cypher($expression = 'MATCH n RETURN n');

        $this->assertInstanceOf(Query::class, $cypher);
        $this->assertSame($expression, $cypher->cypher());
    }

    public function testParameters()
    {
        $cypher = new Cypher('foo');

        $this->assertFalse($cypher->hasParameters());
        $cypher2 = $cypher->withParameters(['foo' => 'bar']);
        $this->assertNotSame($cypher, $cypher2);
        $this->assertInstanceOf(Query::class, $cypher2);
        $this->assertFalse($cypher->hasParameters());
        $this->assertTrue($cypher2->hasParameters());
        $this->assertCount(1, $cypher2->parameters());
        $this->assertSame('string', (string) $cypher2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $cypher2->parameters()->valueType());
        $this->assertSame('string', (string) $cypher->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $cypher->parameters()->valueType());
    }

    public function testThrowWhenEmptyCypher()
    {
        $this->expectException(DomainException::class);

        new Cypher('');
    }

    public function testThrowWhenEmptyParameterKey()
    {
        $this->expectException(DomainException::class);

        (new Cypher('foo'))->withParameter('', 'foo');
    }
}
