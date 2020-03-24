<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface Transport
{
    public function execute(Query $query): Result;

    /**
     * Check if the server is up and running
     *
     * @throws ServerDown if it doesn't respond
     */
    public function ping(): void;
}
