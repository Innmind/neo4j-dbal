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
            $labels = Set::of('string'),
            $properties = Map::of('string', 'scalar|array')
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
            Map::of('string', 'scalar|array')
                ('foo', 'bar')
        );

        $this->assertTrue($node->hasLabels());
        $this->assertTrue($node->hasProperties());
    }

    public function testThrowWhenInvalidLabelSet()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type Set<string>');

        new Node(
            new Id(42),
            Set::of('str'),
            Map::of('string', 'scalar|array')
        );
    }

    public function testThrowWhenInvalidPropertyMap()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 3 must be of type Map<string, scalar|array>');

        new Node(
            new Id(42),
            Set::of('string'),
            Map::of('string', 'scalar')
        );
    }
}
