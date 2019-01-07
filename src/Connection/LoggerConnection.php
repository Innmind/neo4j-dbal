<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    Connection as ConnectionInterface,
    Query,
    Result,
    Exception\QueryFailed,
    Query\Parameter,
};
use Psr\Log\LoggerInterface;

final class LoggerConnection implements ConnectionInterface
{
    private $connection;
    private $logger;

    public function __construct(
        ConnectionInterface $connection,
        LoggerInterface $logger
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function execute(Query $query): Result
    {
        try {
            $this->logger->debug(
                'Query about to be executed',
                [
                    'cypher' => $query->cypher(),
                    'parameters' => $query
                        ->parameters()
                        ->reduce(
                            [],
                            function(array $carry, string $key, Parameter $parameter): array {
                                $carry[$parameter->key()] = $parameter->value();

                                return $carry;
                            }
                        ),
                ]
            );

            return $this->connection->execute($query);
        } catch (QueryFailed $e) {
            $this->logger->error(
                'Query failed',
                [
                    'message' => (string) $e->response()->body(),
                ]
            );
            throw $e;
        }
    }

    public function openTransaction(): ConnectionInterface
    {
        $this->connection->openTransaction();
        $this->logger->debug('Transaction opened');

        return $this;
    }

    public function isTransactionOpened(): bool
    {
        return $this->connection->isTransactionOpened();
    }

    public function commit(): ConnectionInterface
    {
        $this->connection->commit();
        $this->logger->debug('Transaction committed');

        return $this;
    }

    public function rollback(): ConnectionInterface
    {
        $this->connection->rollback();
        $this->logger->debug('Transaction rollbacked');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAlive(): bool
    {
        return $this->connection->isAlive();
    }
}
