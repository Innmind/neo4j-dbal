<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface ConnectionInterface
{
    /**
     * Execute a cypher query
     *
     * @param QueryInterface $query
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query): ResultInterface;

    /**
     * Open a new transaction
     *
     * @return self
     */
    public function openTransaction(): self;

    /**
     * Check if a transaction is opened
     *
     * @return bool
     */
    public function isTransactionOpened(): bool;

    /**
     * Commit the current transaction
     *
     * @return self
     */
    public function commit(): self;

    /**
     * Rollback the current transaction
     *
     * @return self
     */
    public function rollback(): self;

    /**
     * Check if the server is up and running
     *
     * @return bool
     */
    public function isAlive(): bool;
}
