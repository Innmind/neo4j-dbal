<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\UsingClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class UsingClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new UsingClause('INDEX n.foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('USING', $c->identifier());
        $this->assertSame('INDEX n.foo', (string) $c);
    }
}
