<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testId()
    {
        $id = new Id(42);

        $this->assertSame(42, $id->value());
        $this->assertSame('42', (string) $id);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenNegativeId()
    {
        new Id(-1);
    }
}
