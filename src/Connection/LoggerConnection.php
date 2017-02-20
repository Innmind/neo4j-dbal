<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Connection;

use Innmind\Neo4j\DBAL\{
    ConnectionInterface,
    QueryInterface,
    ResultInterface,
    Exception\QueryException,
    Query\Parameter
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

    /**
     * {@inheritdoc}
     */
    public function execute(QueryInterface $query): ResultInterface
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
        } catch (QueryException $e) {
            $this->logger->error(
                'Query failed',
                [
                    'message' => (string) $e->response()->getBody(),
                ]
            );
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function openTransaction(): ConnectionInterface
    {
        $this->connection->openTransaction();
        $this->logger->debug('Transaction opened');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isTransactionOpened(): bool
    {
        return $this->connection->isTransactionOpened();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): ConnectionInterface
    {
        $this->connection->commit();
        $this->logger->debug('Transaction committed');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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
