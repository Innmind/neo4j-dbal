<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\Clause\Expression\Node;
use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\TypedCollectionInterface;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testParameters()
    {
        $n = new Node;

        $n2 = $n->withParameter('foo', 'bar');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Node::class, $n2);
        $this->assertInstanceOf(TypedCollectionInterface::class, $n2->parameters());
        $this->assertSame(1, $n2->parameters()->count());
        $this->assertInstanceOf(Parameter::class, $n2->parameters()->first());
    }

    public function testProperties()
    {
        $n = new Node;

        $n2 = $n->withProperty('foo', '{bar}');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Node::class, $n2);
    }

    public function testCast()
    {
        $this->assertSame('()', (string) new Node);
        $this->assertSame('(a)', (string) new Node('a'));
        $this->assertSame('(a:Foo:Bar)', (string) new Node('a', ['Foo', 'Bar']));
        $this->assertSame('(:Foo:Bar)', (string) new Node(null, ['Foo', 'Bar']));
        $this->assertSame(
            '(a:Foo { key: {value}, another: {where}.value })',
            (string) (new Node('a', ['Foo']))
                ->withProperty('key', '{value}')
                ->withProperty('another', '{where}.value')
                ->withParameter('value', 'foo')
                ->withParameter('where', ['value' => 'bar'])
        );
    }
}
