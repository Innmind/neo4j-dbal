<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testType()
    {
        $type = new Type('foo');

        $this->assertSame('foo', $type->value());
        $this->assertSame('foo', (string) $type);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyType()
    {
        new Type('');
    }
}
