<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    ConnectionFactory,
    ConnectionInterface
};
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    public function testInterface()
    {
        $connection = ConnectionFactory::on('localhost')
            ->for('neo4j', 'neo4j')
            ->build();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
    }
}
