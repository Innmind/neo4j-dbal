<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\Query\Parameter;

class ParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $p = new Parameter('foo', ['value']);
        $this->assertSame('foo', $p->key());
        $this->assertSame(['value'], $p->value());
    }
}
