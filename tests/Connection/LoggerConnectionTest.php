<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection,
    Connection\LoggerConnection,
    Query,
    Result,
    Query\Parameter,
    Exception\QueryFailed,
};
use Innmind\Stream\Readable\Stream;
use Innmind\Immutable\Map;
use Innmind\Http\Message\Response;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class LoggerConnectionTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Connection::class,
            new LoggerConnection(
                $this->createMock(Connection::class),
                $this->createMock(LoggerInterface::class)
            )
        );
    }

    public function testExecute()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $query = $this->createMock(Query::class);
        $inner
            ->expects($this->once())
            ->method('execute')
            ->with($query)
            ->willReturn(
                $result = $this->createMock(Result::class)
            );
        $query
            ->expects($this->once())
            ->method('cypher')
            ->willReturn('foo');
        $query
            ->expects($this->once())
            ->method('parameters')
            ->willReturn(
                Map::of('string', Parameter::class)
                    ('bar', new Parameter('bar', 'baz'))
            );
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                'Query about to be executed',
                [
                    'cypher' => 'foo',
                    'parameters' => ['bar' => 'baz']
                ]
            );

        $this->assertSame($result, $connection->execute($query));
    }

    public function testLogWhenQueryFails()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $query = $this->createMock(Query::class);
        $query
            ->expects($this->any())
            ->method('parameters')
            ->willReturn(Map::of('string', Query\Parameter::class));
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'Query failed',
                ['message' => 'bar']
            );
        $inner
            ->expects($this->once())
            ->method('execute')
            ->with($query)
            ->will(
                $this->throwException(
                    new QueryFailed(
                        $query,
                        $response = $this->createMock(Response::class)
                    )
                )
            );
        $response
            ->expects($this->once())
            ->method('body')
            ->willReturn(Stream::ofContent('bar'));

        $this->expectException(QueryFailed::class);

        $connection->execute($query);
    }

    public function testOpenTransaction()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('openTransaction');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction opened');

        $this->assertNull($connection->openTransaction());
    }

    public function testIsTransactionOpened()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('isTransactionOpened')
            ->willReturn(true);

        $this->assertTrue($connection->isTransactionOpened());
    }

    public function testCommit()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('commit');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction committed');

        $this->assertNull($connection->commit());
    }

    public function testRollback()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('rollback');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction rollbacked');

        $this->assertNull($connection->rollback());
    }

    public function testIsAlive()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(Connection::class),
            $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('isAlive')
            ->willReturn(true);

        $this->assertTrue($connection->isAlive());
    }
}
