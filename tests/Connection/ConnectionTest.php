<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection\Connection,
    Connection as ConnectionInterface,
    Server,
    Authentication,
    Transactions,
    Transport\Http,
    Result,
    Translator\HttpTranslator,
    Query,
    HttpTransport\Transport,
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use function Innmind\HttpTransport\bootstrap as http;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    private $connection;

    public function setUp()
    {
        $server = new Server(
            'http',
            'localhost',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');
        $httpTransport = new Transport(
            $server,
            $auth,
            http()['default']()
        );
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(TimeContinuumInterface::class)
        );
        $this->connection = new Connection(
            new Http(
                new HttpTranslator($transactions),
                $httpTransport
            ),
            $transactions
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(ConnectionInterface::class, $this->connection);
    }

    public function testExecute()
    {
        $query = $this->createMock(Query::class);
        $query
            ->method('cypher')
            ->willReturn('match (n) return n');

        $result = $this->connection->execute($query);

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testTransactions()
    {
        $this->assertFalse($this->connection->isTransactionOpened());
        $this->assertSame($this->connection, $this->connection->openTransaction());
        $this->assertTrue($this->connection->isTransactionOpened());
        $this->assertSame($this->connection, $this->connection->commit());
        $this->assertFalse($this->connection->isTransactionOpened());
        $this->connection->openTransaction();
        $this->assertTrue($this->connection->isTransactionOpened());
        $this->assertSame($this->connection, $this->connection->rollback());
        $this->assertFalse($this->connection->isTransactionOpened());
    }

    public function testAlive()
    {
        $this->assertTrue($this->connection->isAlive());

        $server = new Server(
            'http',
            'localhost',
            1337
        );
        $auth = new Authentication('neo4j', 'ci');
        $httpTransport = new Transport(
            $server,
            $auth,
            http()['default']()
        );
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(TimeContinuumInterface::class)
        );
        $connection = new Connection(
            new Http(
                new HttpTranslator($transactions),
                $httpTransport
            ),
            $transactions
        );

        $this->assertFalse($connection->isAlive());
    }

    public function testConcrete()
    {
        $query = (new Query\Query)
            ->create('n', ['Foo', 'Bar'])
            ->withProperty('foo', '{bar}')
            ->withParameter('bar', 'baz')
            ->return('n');

        $result = $this->connection->execute($query);

        $this->assertSame(1, $result->nodes()->count());
        $this->assertTrue(
            in_array('Bar', $result->nodes()->current()->labels()->toPrimitive())
        );
        $this->assertTrue(
            in_array('Foo', $result->nodes()->current()->labels()->toPrimitive())
        );
        $this->assertCount(1, $result->nodes()->current()->properties());
        $this->assertSame(
            'baz',
            $result->nodes()->current()->properties()->get('foo')
        );
        $this->assertSame(
            'n',
            $result->rows()->current()->column()
        );
        $this->assertSame(
            ['foo' => 'baz'],
            $result->rows()->current()->value()
        );
    }
}
