<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Result;

use Innmind\Neo4j\DBAL\Result\Node;
use Innmind\Neo4j\DBAL\Result\NodeInterface;
use Innmind\Neo4j\DBAL\Result\Id;
use Innmind\Immutable\Collection;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testNode()
    {
        $node = new Node(
            $i = new Id(42),
            $l = new Collection([]),
            $p = new Collection([])
        );

        $this->assertInstanceOf(NodeInterface::class, $node);
        $this->assertSame($i, $node->id());
        $this->assertSame($l, $node->labels());
        $this->assertSame($p, $node->properties());
        $this->assertFalse($node->hasLabels());
        $this->assertFalse($node->hasProperties());

        $node = new Node(
            new Id(42),
            new Collection(['foo']),
            new Collection(['bar'])
        );

        $this->assertTrue($node->hasLabels());
        $this->assertTrue($node->hasProperties());
    }
}
