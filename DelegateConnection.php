<?php

namespace Innmind\Neo4j\DBAL;

class DelegateConnection implements ConnectionInterface
{
    protected $connections = [];
    protected $ordering = [];
    protected $activeConnection;

    /**
     * Add a connection
     *
     * @param string $name
     * @param Connection $conn
     *
     * @return DelegateConnection self
     */
    public function addConnection($name, Connection $conn)
    {
        $this->connections[(string) $name] = $conn;
        $this->ordering[] = (string) $name;

        return $this;
    }

    /**
     * Return a specific connection
     *
     * @param string $name
     *
     * @throws InvalidArgumentException If the connection does not exist
     *
     * @return Connection
     */
    public function getConnection($name)
    {
        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown connection named "%s"',
                $name
            ));
        }

        return $this->connections[$name];
    }

    /**
     * Return the first active connection
     *
     * @throws RuntimeExecption If no connection is alive
     *
     * @return Connection
     */
    public function getActiveConnection()
    {
        if ($this->activeConnection) {
            return $this->activeConnection;
        }

        foreach ($this->getConnections() as $conn) {
            if ($conn->isAlive()) {
                return $this->activeConnection = $conn;
            }
        }

        throw new \RuntimeException('No connection alive');
    }

    /**
     * @inheritdoc
     */
    public function createQuery()
    {
        return $this->delegateCall('createQuery');
    }

    /**
     * @inheritdoc
     */
    public function executeQuery(Query $query)
    {
        return $this->delegateCall('executeQuery', [$query]);
    }

    /**
     * @inheritdoc
     */
    public function execute($query, array $parameters)
    {
        return $this->delegateCall('execute', [$query, $parameters]);
    }

    /**
     * @inheritdoc
     */
    public function executeQueries(array $queries)
    {
        return $this->delegateCall('executeQueries', [$queries]);
    }

    /**
     * @inheritdoc
     */
    public function openTransaction()
    {
        return $this->delegateCall('openTransaction');
    }

    /**
     * @inheritdoc
     */
    public function isTransactionOpened()
    {
        return $this->delegateCall('isTransactionOpened');
    }

    /**
     * @inheritdoc
     */
    public function resetTransactionTimeout()
    {
        return $this->delegateCall('resetTransactionTimeout');
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        return $this->delegateCall('commit');
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        return $this->delegateCall('rollback');
    }

    /**
     * @inheritdoc
     */
    public function isAlive()
    {
        return $this->delegateCall('isAlive');
    }

    /**
     * Delegate the given method call to one of the connections
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    protected function delegateCall($method, array $params = [])
    {
        return call_user_func_array(
            [$this->getActiveConnection(), $method],
            $params
        );
    }

    /**
     * Return the connections in the order it has been defined
     *
     * @return array
     */
    protected function getConnections()
    {
        $conns = $this->connections;
        $ordering = array_flip($this->ordering);

        uksort($conns, function($a, $b) use ($ordering) {
            return $ordering[$a] > $ordering[$b];
        });

        return array_values($conns);
    }
}
