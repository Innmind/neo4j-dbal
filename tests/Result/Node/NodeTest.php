<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result\Node;

use Innmind\Neo4j\DBAL\Result\{
    Node\Node,
    Node as NodeInterface,
    Id,
};
use Innmind\Immutable\{
    Set,
    Map,
};
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testNode()
    {
        $node = new Node(
            $id = new Id(42),
            $labels = new Set('string'),
            $properties = new Map('string', 'variable')
        );

        $this->assertInstanceOf(NodeInterface::class, $node);
        $this->assertSame($id, $node->id());
        $this->assertSame($labels, $node->labels());
        $this->assertSame($properties, $node->properties());
        $this->assertFalse($node->hasLabels());
        $this->assertFalse($node->hasProperties());

        $node = new Node(
            new Id(42),
            Set::of('string', 'foo'),
            Map::of('string', 'variable')
                ('foo', 'bar')
        );

        $this->assertTrue($node->hasLabels());
        $this->assertTrue($node->hasProperties());
    }

    public function testThrowWhenInvalidLabelSet()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type SetInterface<string>');

        new Node(
            new Id(42),
            new Set('str'),
            new Map('string', 'variable')
        );
    }

    public function testThrowWhenInvalidPropertyMap()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 3 must be of type MapInterface<string, variable>');

        new Node(
            new Id(42),
            new Set('string'),
            new Map('string', 'scalar')
        );
    }
}
