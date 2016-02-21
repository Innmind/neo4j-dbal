<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Result;

use Innmind\Neo4j\DBAL\Result\Row;

class RowTest extends \PHPUnit_Framework_TestCase
{
    public function testRow()
    {
        $r = new Row(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $r->value());
    }
}
