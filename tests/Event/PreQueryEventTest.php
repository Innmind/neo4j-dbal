<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Event;

use Innmind\Neo4j\DBAL\{
    Event\PreQueryEvent,
    QueryInterface
};
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class PreQueryEventTest extends TestCase
{
    public function testGetQuery()
    {
        $q = new class implements QueryInterface {
            public function cypher(): string
            {
            }

            public function __toString(): string
            {
            }

            public function parameters(): MapInterface
            {
            }

            public function hasParameters(): bool
            {
            }
        };
        $e = new PreQueryEvent($q);

        $this->assertSame($q, $e->query());
    }
}
