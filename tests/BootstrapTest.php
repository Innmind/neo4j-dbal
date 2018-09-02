<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use function Innmind\Neo4j\DBAL\bootstrap;
use Innmind\Neo4j\DBAL\Connection;
use Innmind\HttpTransport\Transport;
use Innmind\TimeContinuum\TimeContinuumInterface;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testBootstrap()
    {
        $connection = bootstrap(
            $this->createMock(Transport::class),
            $this->createMock(TimeContinuumInterface::class)
        );

        $this->assertInstanceOf(Connection::class, $connection);
    }
}
