<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    ConnectionFactory,
    ConnectionInterface
};
use Innmind\HttpTransport\TransportInterface;
use Innmind\TimeContinuum\TimeContinuumInterface;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    public function testInterface()
    {
        $connection = ConnectionFactory::on('localhost')
            ->for('neo4j', 'neo4j')
            ->useTransport($this->createMock(TransportInterface::class))
            ->build();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
    }

    public function testUseClock()
    {
        $connection = ConnectionFactory::on('localhost')
            ->for('neo4j', 'neo4j')
            ->useTransport($this->createMock(TransportInterface::class))
            ->useClock($this->createMock(TimeContinuumInterface::class))
            ->build();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
    }
}
