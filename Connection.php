<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Connection implements ConnectionInterface
{
    private $transport;
    private $transactions;

    public function __construct(
        TransportInterface $transport,
        Transactions $transactions
    ) {
        $this->transport = $transport;
        $this->transactions = $transactions;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(QueryInterface $query): ResultInterface
    {
        return $this->transport->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function openTransaction(): ConnectionInterface
    {
        $this->transactions->open();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isTransactionOpened(): bool
    {
        return $this->transactions->has();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): ConnectionInterface
    {
        $this->transactions->commit();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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

    public function dispatcher(): EventDispatcherInterface
    {
        return $this->transport->dispatcher();
    }
}
