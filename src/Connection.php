<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface Connection
{
    public function execute(Query $query): Result;
    public function openTransaction(): void;
    public function isTransactionOpened(): bool;
    public function commit(): void;
    public function rollback(): void;

    /**
     * Check if the server is up and running
     */
    public function isAlive(): bool;
}
