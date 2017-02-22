<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    ConnectionInterface,
    Connection\LoggerConnection,
    QueryInterface,
    ResultInterface,
    Query\Parameter,
    Exception\QueryException
};
use Innmind\Filesystem\Stream\StringStream;
use Innmind\Immutable\Map;
use Psr\Log\LoggerInterface;
use Innmind\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;

class LoggerConnectionTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ConnectionInterface::class,
            new LoggerConnection(
                $this->createMock(ConnectionInterface::class),
                $this->createMock(LoggerInterface::class)
            )
        );
    }

    public function testExecute()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
        $inner
            ->expects($this->once())
            ->method('execute')
            ->with($query)
            ->willReturn(
                $result = $this->createMock(ResultInterface::class)
            );
        $query
            ->expects($this->once())
            ->method('cypher')
            ->willReturn('foo');
        $query
            ->expects($this->once())
            ->method('parameters')
            ->willReturn(
                (new Map('string', Parameter::class))
                    ->put('bar', new Parameter('bar', 'baz'))
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

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\QueryException
     */
    public function testLogWhenQueryFails()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $query = $this->createMock(QueryInterface::class);
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
                    QueryException::failed(
                        $query,
                        $response = $this->createMock(ResponseInterface::class)
                    )
                )
            );
        $response
            ->expects($this->once())
            ->method('body')
            ->willReturn(new StringStream('bar'));

        $connection->execute($query);
    }

    public function testOpenTransaction()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('openTransaction');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction opened');

        $this->assertSame($connection, $connection->openTransaction());
    }

    public function testIsTransactionOpened()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
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
            $inner = $this->createMock(ConnectionInterface::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('commit');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction committed');

        $this->assertSame($connection, $connection->commit());
    }

    public function testRollback()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
            $logger = $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('rollback');
        $logger
            ->expects($this->once())
            ->method('debug')
            ->with('Transaction rollbacked');

        $this->assertSame($connection, $connection->rollback());
    }

    public function testIsAlive()
    {
        $connection = new LoggerConnection(
            $inner = $this->createMock(ConnectionInterface::class),
            $this->createMock(LoggerInterface::class)
        );
        $inner
            ->expects($this->once())
            ->method('isAlive')
            ->willReturn(true);

        $this->assertTrue($connection->isAlive());
    }
}
