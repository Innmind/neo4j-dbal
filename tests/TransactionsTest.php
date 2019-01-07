<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Transactions,
    Transaction,
    HttpTransport\Transport,
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Url\Url;
use Innmind\Immutable\Exception\OutOfBoundException;
use function Innmind\HttpTransport\bootstrap as http;
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    private $transactions;
    private $server;

    public function setUp()
    {
        $this->server = 'http://localhost:7474/';

        $this->transactions = new Transactions(
            new Transport(
                Url::fromString('http://neo4j:ci@localhost:7474/'),
                http()['default']()
            ),
            $this->createMock(TimeContinuumInterface::class)
        );
    }

    public function testOpen()
    {
        $this->assertFalse($this->transactions->isOpened());
        $transaction = $this->transactions->open();

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertRegExp(
            '|' . (string) $this->server . 'db/data/transaction/\d+|',
            (string) $transaction->endpoint()
        );
        $this->assertRegExp(
            '|' . (string) $this->server . 'db/data/transaction/\d+/commit|',
            (string) $transaction->commitEndpoint()
        );
        $this->assertTrue($this->transactions->isOpened());
        $this->assertSame($transaction, $this->transactions->current());
    }

    public function testCommit()
    {
        $this->transactions->open();
        $this->assertSame(
            $this->transactions,
            $this->transactions->commit()
        );
        $this->assertFalse($this->transactions->isOpened());
    }

    public function testThrowWhenAskingForTransactionWhenThereIsNone()
    {
        $this->expectException(OutOfBoundException::class);

        $this->transactions->current();
    }

    public function testThrowWhenAskingForCommitWhenThereIsNoTransaction()
    {
        $this->expectException(OutOfBoundException::class);

        $this->transactions->commit();
    }

    public function testThrowWhenAskingForRollbackWhenThereIsNoTransaction()
    {
        $this->expectException(OutOfBoundException::class);

        $this->transactions->rollback();
    }

    public function testRollback()
    {
        $this->transactions->open();
        $this->assertSame(
            $this->transactions,
            $this->transactions->rollback()
        );
        $this->assertFalse($this->transactions->isOpened());
    }
}
