<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Relationship;
use Innmind\Neo4j\DBAL\Result\RelationshipInterface;
use Innmind\Neo4j\DBAL\Result\Id;
use Innmind\Neo4j\DBAL\Result\Type;
use Innmind\Immutable\Collection;

class RelationshipTest extends \PHPUnit_Framework_TestCase
{
    public function testRelationship()
    {
        $r = new Relationship(
            $i = new Id(42),
            $t = new Type('foo'),
            $s = new Id(24),
            $e = new Id(66),
            $p = new Collection(['foo' => 'bar'])
        );

        $this->assertInstanceOf(RelationshipInterface::class, $r);
        $this->assertSame($i, $r->id());
        $this->assertSame($t, $r->type());
        $this->assertSame($s, $r->startNode());
        $this->assertSame($e, $r->endNode());
        $this->assertSame($p, $r->properties());
        $this->assertTrue($r->hasProperties());

        $r = new Relationship(
            $i = new Id(42),
            $t = new Type('foo'),
            $s = new Id(24),
            $e = new Id(66),
            $p = new Collection([])
        );

        $this->assertFalse($r->hasProperties());
    }
}
