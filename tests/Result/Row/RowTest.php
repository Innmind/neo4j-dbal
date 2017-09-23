<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result\Row;

use Innmind\Neo4j\DBAL\Result\{
    Row\Row,
    Row as RowInterface
};
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testRow()
    {
        $r = new Row('baz', ['foo' => 'bar']);

        $this->assertInstanceOf(RowInterface::class, $r);
        $this->assertSame(['foo' => 'bar'], $r->value());
        $this->assertSame('baz', $r->column());
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyColumn()
    {
        new Row('', 'foo');
    }
}
