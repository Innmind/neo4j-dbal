<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship,
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testParameters()
    {
        $relationship = Relationship::both();

        $relationship2 = $relationship->withParameter('foo', 'bar');
        $this->assertNotSame($relationship, $relationship2);
        $this->assertInstanceOf(Relationship::class, $relationship2);
        $this->assertInstanceOf(Map::class, $relationship2->parameters());
        $this->assertSame('string', (string) $relationship2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $relationship2->parameters()->valueType());
        $this->assertCount(1, $relationship2->parameters());
    }

    public function testProperties()
    {
        $relationship = Relationship::both();

        $relationship2 = $relationship->withProperty('foo', '{bar}');
        $this->assertNotSame($relationship, $relationship2);
        $this->assertInstanceOf(Relationship::class, $relationship2);
    }

    public function testCast()
    {
        $this->assertSame('-[]-', Relationship::both()->cypher());
        $this->assertSame('-[a]-', Relationship::both('a')->cypher());
        $this->assertSame('-[:FOO]-', Relationship::both(null, 'FOO')->cypher());
        $this->assertSame('-[a:FOO]-', Relationship::both('a', 'FOO')->cypher());
        $this->assertSame('<-[a:FOO]-', Relationship::left('a', 'FOO')->cypher());
        $this->assertSame('-[a:FOO]->', Relationship::right('a', 'FOO')->cypher());
        $this->assertSame('-[]-', Relationship::both()->withADistanceOf(1)->cypher());
        $this->assertSame('-[*2]-', Relationship::both()->withADistanceOf(2)->cypher());
        $this->assertSame('-[*2..3]-', Relationship::both()->withADistanceBetween(2, 3)->cypher());
        $this->assertSame('-[*3..]-', Relationship::both()->withADistanceOfAtLeast(3)->cypher());
        $this->assertSame('-[*..3]-', Relationship::both()->withADistanceOfAtMost(3)->cypher());
        $this->assertSame('-[*]-', Relationship::both()->withAnyDistance(3)->cypher());
        $this->assertSame(
            '-[a:FOO*24..42 { key: {value}, another: {where}.value }]->',
            Relationship::right('a', 'FOO')
                ->withADistanceBetween(24, 42)
                ->withProperty('key', '{value}')
                ->withProperty('another', '{where}.value')
                ->withParameter('value', 'foo')
                ->withParameter('where', ['value' => 'bar'])
                ->cypher()
        );
    }

    public function testThrowWhenEmptyParameterKey()
    {
        $this->expectException(DomainException::class);

        Relationship::both()->withParameter('', 'foo');
    }

    public function testThrowWhenEmptyPropertyName()
    {
        $this->expectException(DomainException::class);

        Relationship::both()->withProperty('', 'foo');
    }
}
