<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\{
    Node,
    NodeInterface,
    Id
};
use Innmind\Immutable\{
    Set,
    Map
};
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testNode()
    {
        $node = new Node(
            $i = new Id(42),
            $l = new Set('string'),
            $p = new Map('string', 'variable')
        );

        $this->assertInstanceOf(NodeInterface::class, $node);
        $this->assertSame($i, $node->id());
        $this->assertSame($l, $node->labels());
        $this->assertSame($p, $node->properties());
        $this->assertFalse($node->hasLabels());
        $this->assertFalse($node->hasProperties());

        $node = new Node(
            new Id(42),
            (new Set('string'))->add('foo'),
            (new Map('string', 'variable'))
                ->put('foo', 'bar')
        );

        $this->assertTrue($node->hasLabels());
        $this->assertTrue($node->hasProperties());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidLabelSet()
    {
        new Node(
            new Id(42),
            new Set('str'),
            new Map('string', 'variable')
        );
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidPropertyMap()
    {
        new Node(
            new Id(42),
            new Set('string'),
            new Map('string', 'scalar')
        );
    }
}
