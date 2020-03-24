<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection as ConnectionInterface,
    Transport,
    Transactions,
    Query,
    Result,
};

final class Connection implements ConnectionInterface
{
    private Transport $transport;
    private Transactions $transactions;

    public function __construct(
        Transport $transport,
        Transactions $transactions
    ) {
        $this->transport = $transport;
        $this->transactions = $transactions;
    }

    public function execute(Query $query): Result
    {
        return $this->transport->execute($query);
    }

    public function openTransaction(): void
    {
        $this->transactions->open();
    }

    public function isTransactionOpened(): bool
    {
        return $this->transactions->isOpened();
    }

    public function commit(): void
    {
        $this->transactions->commit();
    }

    public function rollback(): void
    {
        $this->transactions->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function isAlive(): bool
    {
        try {
            $this->transport->ping();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
