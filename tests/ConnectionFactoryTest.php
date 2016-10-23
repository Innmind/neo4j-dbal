<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    ConnectionFactory,
    ConnectionInterface
};
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $connection = ConnectionFactory::on('localhost')
            ->for('neo4j', 'neo4j')
            ->useDispatcher($d = new EventDispatcher)
            ->build();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertSame($d, $connection->dispatcher());

        $connection = ConnectionFactory::on('localhost')
            ->for('neo4j', 'neo4j')
            ->build();

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertInstanceOf(EventDispatcher::class, $connection->dispatcher());
    }
}
