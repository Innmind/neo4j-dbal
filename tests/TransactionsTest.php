<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Transactions,
    Transaction,
    Server,
    Authentication,
    HttpTransport\Transport
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\HttpTransport\GuzzleTransport;
use Innmind\Http\{
    Translator\Response\Psr7Translator,
    Factory\Header\Factories
};
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    private $transactions;
    private $server;

    public function setUp()
    {
        $this->server = new Server(
            'http',
            'localhost',
            7474
        );
        $auth = new Authentication('neo4j', 'ci');

        $this->transactions = new Transactions(
            new Transport(
                $this->server,
                $auth,
                new GuzzleTransport(
                    new Client,
                    new Psr7Translator(
                        Factories::default()
                    )
                )
            ),
            $this->createMock(TimeContinuumInterface::class)
        );
    }

    public function testOpen()
    {
        $this->assertFalse($this->transactions->has());
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
        $this->assertTrue($this->transactions->has());
        $this->assertSame($transaction, $this->transactions->get());
    }

    public function testCommit()
    {
        $this->transactions->open();
        $this->assertSame(
            $this->transactions,
            $this->transactions->commit()
        );
        $this->assertFalse($this->transactions->has());
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForTransactionWhenThereIsNone()
    {
        $this->transactions->get();
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForCommitWhenThereIsNoTransaction()
    {
        $this->transactions->commit();
    }

    /**
     * @expectedException Innmind\Immutable\Exception\OutOfBoundException
     */
    public function testThrowWhenAskingForRollbackWhenThereIsNoTransaction()
    {
        $this->transactions->rollback();
    }

    public function testRollback()
    {
        $this->transactions->open();
        $this->assertSame(
            $this->transactions,
            $this->transactions->rollback()
        );
        $this->assertFalse($this->transactions->has());
    }
}
