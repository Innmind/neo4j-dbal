<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests\Event;

use Innmind\Neo4j\DBAL\Event\PostQueryEvent;
use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Neo4j\DBAL\ResultInterface;
use Innmind\Immutable\TypedCollectionInterface;

class PostQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
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
        $r = new class implements ResultInterface {
            public function getNodes(): TypedCollectionInterface
            {
            }

            public function getRelationships(): TypedCollectionInterface
            {
            }

            public function getRows(): TypedCollectionInterface
            {
            }
        };
        $e = new PostQueryEvent($q, $r);

        $this->assertSame($q, $e->getQuery());
        $this->assertSame($r, $e->getResult());
    }
}
