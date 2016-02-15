<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Result;

use Innmind\Neo4j\DBAL\Result\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $t = new Type('foo');

        $this->assertSame('foo', $t->value());
        $this->assertSame('foo', (string) $t);
    }
}
