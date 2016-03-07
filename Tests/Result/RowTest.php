<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Result;

use Innmind\Neo4j\DBAL\Result\Row;

class RowTest extends \PHPUnit_Framework_TestCase
{
    public function testRow()
    {
        $r = new Row('baz', ['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $r->value());
        $this->assertSame('baz', $r->column());
    }
}
