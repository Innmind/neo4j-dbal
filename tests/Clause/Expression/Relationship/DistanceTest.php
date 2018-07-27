<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause\Expression\Relationship;

use Innmind\Neo4j\DBAL\{
    Clause\Expression\Relationship\Distance,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;
use Eris\{
    Generator,
    TestTrait,
};

class DistanceTest extends TestCase
{
    use TestTrait;

    public function testDefault()
    {
        $this->assertSame('', (string) new Distance);
    }

    public function testOf()
    {
        $this
            ->minimumEvaluationRatio(0.01)
            ->forAll(Generator\int())
            ->when(static function(int $int): bool {
                return $int > 1;
            })
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::of($int));
                $this->assertSame('*'.$int, (string) Distance::of($int));
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
            ->minimumEvaluationRatio(0.01)
            ->forAll(Generator\int(), Generator\int())
            ->when(static function(int $min, int $max): bool {
                return $min >= 0 && $min !== $max && $max > $min;
            })
            ->then(function(int $min, int $max): void {
                $this->assertInstanceOf(Distance::class, Distance::between($min, $max));
                $this->assertSame("*$min..$max", (string) Distance::between($min, $max));
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
            ->minimumEvaluationRatio(0.01)
            ->forAll(Generator\int())
            ->when(static function(int $int): bool {
                return $int > 1;
            })
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::atLeast($int));
                $this->assertSame("*$int..", (string) Distance::atLeast($int));
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
            ->minimumEvaluationRatio(0.01)
            ->forAll(Generator\int())
            ->when(static function(int $int): bool {
                return $int > 1;
            })
            ->then(function(int $int): void {
                $this->assertInstanceOf(Distance::class, Distance::atMost($int));
                $this->assertSame("*..$int", (string) Distance::atMost($int));
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
        $this->assertSame('*', (string) Distance::any());
    }
}
