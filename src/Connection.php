<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface Connection
{
    public function execute(Query $query): Result;
    public function openTransaction(): self;
    public function isTransactionOpened(): bool;
    public function commit(): self;
    public function rollback(): self;

    /**
     * Check if the server is up and running
     */
    public function isAlive(): bool;
}
