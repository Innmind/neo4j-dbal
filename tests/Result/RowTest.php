<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRow()
    {
        $r = new Row('baz', ['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $r->value());
        $this->assertSame('baz', $r->column());
    }
}
