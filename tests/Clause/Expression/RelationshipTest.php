<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression;

use Innmind\Neo4j\DBAL\Clause\Expression\Relationship;
use Innmind\Neo4j\DBAL\Query\Parameter;
use Innmind\Immutable\TypedCollectionInterface;

class RelationshipTest extends \PHPUnit_Framework_TestCase
{
    public function testParameters()
    {
        $n = new Relationship;

        $n2 = $n->withParameter('foo', 'bar');
        $this->assertNotSame($n, $n2);
        $this->assertInstanceOf(Relationship::class, $n2);
        $this->assertInstanceOf(TypedCollectionInterface::class, $n2->parameters());
        $this->assertSame(1, $n2->parameters()->count());
        $this->assertInstanceOf(Parameter::class, $n2->parameters()->first());
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
        $this->assertSame(
            '-[a:FOO { key: {value}, another: {where}.value }]->',
            (string) (new Relationship('a', 'FOO', Relationship::RIGHT))
                ->withProperty('key', '{value}')
                ->withProperty('another', '{where}.value')
                ->withParameter('value', 'foo')
                ->withParameter('where', ['value' => 'bar'])
        );
    }
}
