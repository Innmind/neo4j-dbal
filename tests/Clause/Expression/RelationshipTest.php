<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship,
    Query\Parameter
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testParameters()
    {
        $n = new Relationship;

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
        $n = new Relationship;

        $n2 = $n->withProperty('foo', '{bar}');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Relationship::class, $n2);
    }

    public function testCast()
    {
        $this->assertSame('-[]-', (string) new Relationship);
        $this->assertSame('-[a]-', (string) new Relationship('a'));
        $this->assertSame('-[:FOO]-', (string) new Relationship(null, 'FOO'));
        $this->assertSame('-[a:FOO]-', (string) new Relationship('a', 'FOO'));
        $this->assertSame('<-[a:FOO]-', (string) new Relationship('a', 'FOO', Relationship::LEFT));
        $this->assertSame('-[a:FOO]->', (string) new Relationship('a', 'FOO', Relationship::RIGHT));
        $this->assertSame('-[]-', (string) (new Relationship)->withADistanceOf(1));
        $this->assertSame('-[*2]-', (string) (new Relationship)->withADistanceOf(2));
        $this->assertSame('-[*2..3]-', (string) (new Relationship)->withADistanceBetween(2, 3));
        $this->assertSame('-[*3..]-', (string) (new Relationship)->withADistanceOfAtLeast(3));
        $this->assertSame('-[*..3]-', (string) (new Relationship)->withADistanceOfAtMost(3));
        $this->assertSame('-[*]-', (string) (new Relationship)->withAnyDistance(3));
        $this->assertSame(
            '-[a:FOO*24..42 { key: {value}, another: {where}.value }]->',
            (string) (new Relationship('a', 'FOO', Relationship::RIGHT))
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
        (new Relationship)->withParameter('', 'foo');
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyPropertyName()
    {
        (new Relationship)->withProperty('', 'foo');
    }
}
