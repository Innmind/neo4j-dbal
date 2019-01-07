<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship,
    Query\Parameter,
    Exception\DomainException,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testParameters()
    {
        $relationship = Relationship::both();

        $relationship2 = $relationship->withParameter('foo', 'bar');
        $this->assertNotSame($relationship, $relationship2);
        $this->assertInstanceOf(Relationship::class, $relationship2);
        $this->assertInstanceOf(MapInterface::class, $relationship2->parameters());
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
        $this->assertSame('-[]-', (string) Relationship::both());
        $this->assertSame('-[a]-', (string) Relationship::both('a'));
        $this->assertSame('-[:FOO]-', (string) Relationship::both(null, 'FOO'));
        $this->assertSame('-[a:FOO]-', (string) Relationship::both('a', 'FOO'));
        $this->assertSame('<-[a:FOO]-', (string) Relationship::left('a', 'FOO'));
        $this->assertSame('-[a:FOO]->', (string) Relationship::right('a', 'FOO'));
        $this->assertSame('-[]-', (string) Relationship::both()->withADistanceOf(1));
        $this->assertSame('-[*2]-', (string) Relationship::both()->withADistanceOf(2));
        $this->assertSame('-[*2..3]-', (string) Relationship::both()->withADistanceBetween(2, 3));
        $this->assertSame('-[*3..]-', (string) Relationship::both()->withADistanceOfAtLeast(3));
        $this->assertSame('-[*..3]-', (string) Relationship::both()->withADistanceOfAtMost(3));
        $this->assertSame('-[*]-', (string) Relationship::both()->withAnyDistance(3));
        $this->assertSame(
            '-[a:FOO*24..42 { key: {value}, another: {where}.value }]->',
            (string) Relationship::right('a', 'FOO')
                ->withADistanceBetween(24, 42)
                ->withProperty('key', '{value}')
                ->withProperty('another', '{where}.value')
                ->withParameter('value', 'foo')
                ->withParameter('where', ['value' => 'bar'])
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
