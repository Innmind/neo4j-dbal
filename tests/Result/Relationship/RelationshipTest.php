<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result\Relationship;

use Innmind\Neo4j\DBAL\Result\{
    Relationship\Relationship,
    Relationship as RelationshipInterface,
    Id,
    Type,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testRelationship()
    {
        $relationship = new Relationship(
            $id = new Id(42),
            $type = new Type('foo'),
            $startNode = new Id(24),
            $endNode = new Id(66),
            $properties = Map::of('string', 'scalar|array')
                ('foo', 'bar')
        );

        $this->assertInstanceOf(RelationshipInterface::class, $relationship);
        $this->assertSame($id, $relationship->id());
        $this->assertSame($type, $relationship->type());
        $this->assertSame($startNode, $relationship->startNode());
        $this->assertSame($endNode, $relationship->endNode());
        $this->assertSame($properties, $relationship->properties());
        $this->assertTrue($relationship->hasProperties());

        $relationship = new Relationship(
            new Id(42),
            new Type('foo'),
            new Id(24),
            new Id(66),
            Map::of('string', 'scalar|array')
        );

        $this->assertFalse($relationship->hasProperties());
    }

    public function testThrowWhenInvalidPropertyMap()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 5 must be of type Map<string, scalar|array>');

        new Relationship(
            new Id(42),
            new Type('foo'),
            new Id(24),
            new Id(66),
            Map::of('string', 'scalar')
        );
    }
}
