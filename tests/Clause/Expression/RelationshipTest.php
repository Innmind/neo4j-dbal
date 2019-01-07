<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship,
    Query\Parameter,
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testParameters()
    {
        $n = Relationship::both();

        $n2 = $n->withParameter('foo', 'bar');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Relationship::class, $n2);
        $this->assertInstanceOf(MapInterface::class, $n2->parameters());
        $this->assertSame('string', (string) $n2->parameters()->keyType());
        $this->assertSame(Parameter::class, (string) $n2->parameters()->valueType());
        $this->assertCount(1, $n2->parameters());
    }

    public function testProperties()
    {
        $n = Relationship::both();

        $n2 = $n->withProperty('foo', '{bar}');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Relationship::class, $n2);
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

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyParameterKey()
    {
        Relationship::both()->withParameter('', 'foo');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyPropertyName()
    {
        Relationship::both()->withProperty('', 'foo');
    }
}
