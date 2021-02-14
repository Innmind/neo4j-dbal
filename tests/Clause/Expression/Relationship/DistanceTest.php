<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression\Relationship;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship\Distance,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class DistanceTest extends TestCase
{
    use BlackBox;

    public function testDefault()
    {
        $this->assertSame('', (new Distance)->cypher());
    }

    public function testOf()
    {
        $this
            ->forAll(Set\Integers::above(2))
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::of($int));
                $this->assertSame('*'.$int, Distance::of($int)->cypher());
            });
    }

    public function testThrowWhenDistanceTooLow()
    {
        $this->expectException(DomainException::class);

        Distance::of(0);
    }

    public function testBetween()
    {
        $this
            ->forAll(Set\Integers::above(0), Set\Integers::above(1))
            ->filter(static function(int $min, int $max): bool {
                return $max > $min;
            })
            ->then(function(int $min, int $max): void {
                $this->assertInstanceOf(Distance::class, Distance::between($min, $max));
                $this->assertSame("*$min..$max", Distance::between($min, $max)->cypher());
            });
    }

    public function testThrowWhenBetweenMinIsTooLow()
    {
        $this->expectException(DomainException::class);

        Distance::between(-1, 1);
    }

    public function testThrowWhenBetweenMaxBelowMin()
    {
        $this->expectException(DomainException::class);

        Distance::between(2, 1);
    }

    public function testAtLeast()
    {
        $this
            ->forAll(Set\Integers::above(2))
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::atLeast($int));
                $this->assertSame("*$int..", Distance::atLeast($int)->cypher());
            });
    }

    public function testThrowWhenMinDistanceTooLow()
    {
        $this->expectException(DomainException::class);

        Distance::atLeast(0);
    }

    public function testAtMost()
    {
        $this
            ->forAll(Set\Integers::above(2))
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::atMost($int));
                $this->assertSame("*..$int", Distance::atMost($int)->cypher());
            });
    }

    public function testThrowWhenMaxDistanceTooLow()
    {
        $this->expectException(DomainException::class);

        Distance::atMost(1);
    }

    public function testAny()
    {
        $this->assertInstanceOf(Distance::class, Distance::any());
        $this->assertSame('*', Distance::any()->cypher());
    }
}
