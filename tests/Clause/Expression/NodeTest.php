<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Node,
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testParameters()
    {
        $node = new Node;

        $node2 = $node->withParameter('foo', 'bar');
        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Node::class, $node2);
        $this->assertInstanceOf(Map::class, $node2->parameters());
        $this->assertSame(
            'string',
            (string) $node2->parameters()->keyType()
        );
        $this->assertSame(
            Parameter::class,
            (string) $node2->parameters()->valueType()
        );
        $this->assertCount(1, $node2->parameters());
    }

    public function testProperties()
    {
        $node = new Node;

        $node2 = $node->withProperty('foo', '{bar}');
        $this->assertNotSame($node, $node2);
        $this->assertInstanceOf(Node::class, $node2);
    }

    public function testCast()
    {
        $this->assertSame('()', (new Node)->cypher());
        $this->assertSame('(a)', (new Node('a'))->cypher());
        $this->assertSame('(a:Foo:Bar)', (new Node('a', 'Foo', 'Bar'))->cypher());
        $this->assertSame('(:Foo:Bar)', (new Node(null, 'Foo', 'Bar'))->cypher());
        $this->assertSame(
            '(a:Foo { key: {value}, another: {where}.value })',
            (new Node('a', 'Foo'))
                ->withProperty('key', '{value}')
                ->withProperty('another', '{where}.value')
                ->withParameter('value', 'foo')
                ->withParameter('where', ['value' => 'bar'])
                ->cypher(),
        );
    }

    public function testThrowWhenEmptyParameterKey()
    {
        $this->expectException(DomainException::class);

        (new Node)->withParameter('', 'foo');
    }

    public function testThrowWhenEmptyPropertyName()
    {
        $this->expectException(DomainException::class);

        (new Node)->withProperty('', 'foo');
    }
}
