<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\Query\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    public function testInterface()
    {
        $p = new Parameter('foo', ['value']);
        $this->assertSame('foo', $p->key());
        $this->assertSame(['value'], $p->value());
    }
}
