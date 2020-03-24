<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\{
    Result\Id,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testId()
    {
        $id = new Id(42);

        $this->assertSame(42, $id->value());
        $this->assertSame('42', $id->toString());
    }

    public function testThrowWhenNegativeId()
    {
        $this->expectException(DomainException::class);

        new Id(-1);
    }
}
