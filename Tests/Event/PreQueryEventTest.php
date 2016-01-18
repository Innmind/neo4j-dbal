<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Event;

use Innmind\Neo4j\DBAL\Event\PreQueryEvent;
use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Immutable\TypedCollectionInterface;

class PreQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $q = new class implements QueryInterface {
            public function getCypher(): string
            {
            }

            public function __toString(): string
            {
            }

            public function getParameters(): TypedCollectionInterface
            {
            }

            public function hasParameters(): bool
            {
            }
        };
        $e = new PreQueryEvent($q);

        $this->assertSame($q, $e->getQuery());
    }
}
