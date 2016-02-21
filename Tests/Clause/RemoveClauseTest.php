<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\RemoveClause;
use Innmind\Neo4j\DBAL\ClauseInterface;

class RemoveClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new RemoveClause('n:Foo');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('REMOVE', $c->identifier());
        $this->assertSame('n:Foo', (string) $c);
    }
}
