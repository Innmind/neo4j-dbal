<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result\Relationship;

use Innmind\Neo4j\DBAL\Result\{
    Relationship\Relationship,
    Relationship as RelationshipInterface,
    Id,
    Type
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testRelationship()
    {
        $r = new Relationship(
            $i = new Id(42),
            $t = new Type('foo'),
            $s = new Id(24),
            $e = new Id(66),
            $p = (new Map('string', 'variable'))
                ->put('foo', 'bar')
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
            $p = new Map('string', 'variable')
        );

        $this->assertFalse($r->hasProperties());
    }

    /**
     * @expectedException TypeError
     * @expectedExceptionMessage Argument 5 must be of type MapInterface<string, variable>
     */
    public function testThrowWhenInvalidPropertyMap()
    {
        new Relationship(
            new Id(42),
            new Type('foo'),
            new Id(24),
            new Id(66),
            new Map('string', 'scalar')
        );
    }
}
