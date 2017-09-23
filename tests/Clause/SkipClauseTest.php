<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause\SkipClause,
    Clause,
    Query\Parameter
};
use PHPUnit\Framework\TestCase;

class SkipClauseTest extends TestCase
{
    public function testInterface()
    {
        $c = new SkipClause('42');

        $this->assertInstanceOf(Clause::class, $c);
        $this->assertSame('SKIP', $c->identifier());
        $this->assertSame('42', (string) $c);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\InvalidArgumentException
     */
    public function testThrowWhenEmptyCypher()
    {
        new SkipClause('');
    }
}
