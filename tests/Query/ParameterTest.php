<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\Query\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    public function testInterface()
    {
        $parameter = new Parameter('foo', ['value']);
        $this->assertSame('foo', $parameter->key());
        $this->assertSame(['value'], $parameter->value());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\DomainException
     */
    public function testThrowWhenEmptyKey()
    {
        new Parameter('', 'foo');
    }
}
