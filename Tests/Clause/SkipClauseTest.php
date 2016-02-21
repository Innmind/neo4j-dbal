<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Clause;

use Innmind\Neo4j\DBAL\Clause\SkipClause;
use Innmind\Neo4j\DBAL\ClauseInterface;
use Innmind\Neo4j\DBAL\Query\Parameter;

class SkipClauseTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $c = new SkipClause('42');

        $this->assertInstanceOf(ClauseInterface::class, $c);
        $this->assertSame('SKIP', $c->identifier());
        $this->assertSame('42', (string) $c);
    }
}
