<?php

namespace Innmind\Neo4j\DBAL;

interface ConnectionInterface
{
    /**
     * Create a query instance
     *
     * @return Query
     */
    public function createQuery();

    /**
     * Execute a query object
     *
     * @param Query $query
     *
     * @return mixed
     */
    public function executeQuery(Query $query);

    /**
     * Execute the given cypher query
     *
     * @param string $query
     * @param array $parameters
     *
     * @return mixed
     */
    public function execute($query, array $parameters);

    /**
     * Execute multiple queries in a single API call
     *
     * @param array $queries
     *
     * @return array
     */
    public function executeQueries(array $queries);

    /**
     * Open a new transaction
     *
     * @throws LogicException If a transaction is already opened
     *
     * @return ConnectionInterface self
     */
    public function openTransaction();

    /**
     * Check if a transaction is already opened
     *
     * @return bool
     */
    public function isTransactionOpened();

    /**
     * Reset the timeout before the transaction closing
     *
     * @throws LogicException If no transaction opened
     *
     * @return ConnectionInterface self
     */
    public function resetTransactionTimeout();

    /**
     * Commit the current transaction
     *
     * @throws LogicException If no transaction opened
     *
     * @return ConnectionInterface self
     */
    public function commit();

    /**
     * Rollback the current transaction
     *
     * @throws LogicException If no transaction opened
     *
     * @return ConnectionInterface self
     */
    public function rollback();

    /**
     * Check if the connection is working
     * (meaning the server is up and reachable)
     *
     * @return bool
     */
    public function isAlive();
}
