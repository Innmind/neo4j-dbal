<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\OnCreateClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class OnCreateClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new OnCreateClause('SET n:Foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('ON CREATE', $c->identifier());
        $this->assertSame('SET n:Foo', (string) $c);
    }
}
