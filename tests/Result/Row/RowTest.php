<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result\Row;

use Innmind\Neo4j\DBAL\{
    Result\Row\Row,
    Result\Row as RowInterface,
    Exception\DomainException,
};
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRow()
    {
        $row = new Row('baz', ['foo' => 'bar']);

        $this->assertInstanceOf(RowInterface::class, $row);
        $this->assertSame(['foo' => 'bar'], $row->value());
        $this->assertSame('baz', $row->column());
    }

    public function testThrowWhenEmptyColumn()
    {
        $this->expectException(DomainException::class);

        new Row('', 'foo');
    }
}
