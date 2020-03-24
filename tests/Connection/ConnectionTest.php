<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection\Connection,
    Connection as ConnectionInterface,
    Transactions,
    Transport\Http,
    Result,
    Translator\HttpTranslator,
    Query,
    HttpTransport\Transport,
};
use Innmind\TimeContinuum\Clock;
use Innmind\Url\Url;
use function Innmind\HttpTransport\bootstrap as http;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    private $connection;

    public function setUp(): void
    {
        $httpTransport = new Transport(
            Url::of('http://neo4j:ci@localhost:7474/'),
            http()['default']()
        );
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(Clock::class)
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
        $this->assertNull($this->connection->openTransaction());
        $this->assertTrue($this->connection->isTransactionOpened());
        $this->assertNull($this->connection->commit());
        $this->assertFalse($this->connection->isTransactionOpened());
        $this->connection->openTransaction();
        $this->assertTrue($this->connection->isTransactionOpened());
        $this->assertNull($this->connection->rollback());
        $this->assertFalse($this->connection->isTransactionOpened());
    }

    public function testAlive()
    {
        $this->assertTrue($this->connection->isAlive());

        $httpTransport = new Transport(
            Url::of('http://neo4j:ci@localhost:1337/'),
            http()['default']()
        );
        $transactions = new Transactions(
            $httpTransport,
            $this->createMock(Clock::class)
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
            ->create('n', 'Foo', 'Bar')
            ->withProperty('foo', '{bar}')
            ->withParameter('bar', 'baz')
            ->return('n');

        $result = $this->connection->execute($query);

        $this->assertSame(1, $result->nodes()->count());
        $this->assertTrue(
            in_array('Bar', unwrap($result->nodes()->values()->first()->labels()))
        );
        $this->assertTrue(
            in_array('Foo', unwrap($result->nodes()->values()->first()->labels()))
        );
        $this->assertCount(1, $result->nodes()->values()->first()->properties());
        $this->assertSame(
            'baz',
            $result->nodes()->values()->first()->properties()->get('foo')
        );
        $this->assertSame(
            'n',
            $result->rows()->first()->column()
        );
        $this->assertSame(
            ['foo' => 'baz'],
            $result->rows()->first()->value()
        );
    }
}
