<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

interface TransportInterface
{
    /**
     * Execute a query
     *
     * @param QueryInterface $query
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query): ResultInterface;

    /**
     * Check if the server is up and running
     *
     * @throws ServerDownException if it doesn't respond
     *
     * @return self
     */
    public function ping(): self;
}
