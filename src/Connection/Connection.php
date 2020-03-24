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

    public function openTransaction(): ConnectionInterface
    {
        $this->transactions->open();

        return $this;
    }

    public function isTransactionOpened(): bool
    {
        return $this->transactions->isOpened();
    }

    public function commit(): ConnectionInterface
    {
        $this->transactions->commit();

        return $this;
    }

    public function rollback(): ConnectionInterface
    {
        $this->transactions->rollback();

        return $this;
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
