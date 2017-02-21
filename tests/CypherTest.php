<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Cypher,
    QueryInterface,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class CypherTest extends TestCase
{
    public function testInterface()
    {
        $c = new Cypher($e = 'MATCH n RETURN n');

        $this->assertInstanceOf(QueryInterface::class, $c);
        $this->assertSame($e, $c->cypher());
        $this->assertSame($e, (string) $c);
    }

    public function testParameters()
    {
        $c = new Cypher('foo');

        $this->assertFalse($c->hasParameters());
        $c2 = $c->withParameters(['foo' => 'bar']);
        $this->assertNotSame($c, $c2);
        $this->assertInstanceOf(QueryInterface::class, $c2);
        $this->assertFalse($c->hasParameters());
        $this->assertTrue($c2->hasParameters());
        $this->assertCount(1, $c2->parameters());
        $this->assertSame('string', (string) $c2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $c2->parameters()->valueType());
        $this->assertSame('string', (string) $c->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $c->parameters()->valueType());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyCypher()
    {
        new Cypher('');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyParameterKey()
    {
        (new Cypher('foo'))->withParameter('', 'foo');
    }
}
