<?php

namespace Innmind\Neo4j\DBAL;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Innmind\Neo4j\DBAL\Event\ApiResponseEvent;
use Innmind\Neo4j\DBAL\Exception\TransactionException;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Client;

class Connection implements ConnectionInterface
{
    protected $http;
    protected $dispatcher;
    protected $cypherBuilder;
    protected $transactionId;

    public function __construct(array $params, EventDispatcherInterface $dispatcher, CypherBuilder $cypherBuilder)
    {
        $resolver = $this->configureOptions();
        $params = $resolver->resolve($params);

        $this->dispatcher = $dispatcher;
        $this->cypherBuilder = $cypherBuilder;

        $headers = [
            'Accept' => 'application/json; charset=UTF-8',
            'Content-Type' => 'application/json',
        ];

        if (isset($params['username']) && isset($params['password'])) {
            $headers['Authorization'] = sprintf(
                'Basic %s',
                base64_encode(sprintf(
                    '%s:%s',
                    $params['username'],
                    $params['password']
                ))
            );
        }

        $this->http = new Client([
            'base_url' => sprintf(
                '%s://%s%s/db/data/',
                $params['scheme'],
                $params['host'],
                $params['port']
            ),
            'defaults' => [
                'headers' => $headers,
                'timeout' => $params['timeout'],
            ],
        ]);

        $this->configureListeners();
    }

    /**
     * Return the event dispatcher associated with this connection
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function createQuery()
    {
        return new Query;
    }

    /**
     * @inheritdoc
     */
    public function executeQuery(Query $query)
    {
        return $this->executeStatements([
            $this->getStatementArray(
                $this->cypherBuilder->getCypher($query),
                $query->getParameters()
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function execute($query, array $parameters)
    {
        return $this->executeStatements([
            $this->getStatementArray(
                (string) $query,
                $parameters
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function openTransaction()
    {
        if ($this->isTransactionOpened()) {
            throw new \LogicException('A transaction is already opened');
        }

        $response = $this->http->post('transaction', [
            'body' => json_encode(['statements' => []])
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new TransactionException(
                'Neo4j failed to open a new transaction',
                TransactionException::OPENING_FAILED
            );
        }

        $this->transactionId = (int) str_replace(
            $this->http->getBaseUrl().'transaction/',
            '',
            $response->getHeader('Location')
        );
    }

    /**
     * @inheritdoc
     */
    public function isTransactionOpened()
    {
        if ($this->transactionId !== null) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function resetTransactionTimeout()
    {
        if (!$this->isTransactionOpened()) {
            throw new \LogicException('No transaction opened');
        }

        $this->http->post('transaction/'.$this->transactionId, [
            'body' => json_encode(['statements' => []])
        ]);
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        if (!$this->isTransactionOpened()) {
            throw new \LogicException('No transaction opened');
        }

        $response = $this->http->post(sprintf('transaction/%s/commit', $this->transactionId), [
            'body' => json_encode(['statements' => []])
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new TransactionException(
                'Transaction commit failed',
                TransactionException::COMMIT_FAILED
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        if (!$this->isTransactionOpened()) {
            throw new \LogicException('No transaction opened');
        }

        $response = $this->http->delete('transaction/'.$this->transactionId);

        if ($response->getStatusCode() !== 200) {
            throw new TransactionException(
                'Transaction rollback failed',
                TransactionException::ROLLBACK_FAILED
            );
        }
    }

    /**
     * Dispatch an event each time a http request is completed
     */
    protected function configureListeners()
    {
        $this->http->getEmitter()->on('complete', function (CompleteEvent $event) {
            if ($response = $event->getResponse()) {
                $this->dispatcher->dispatch(Events::API_RESPONSE, new ApiResponseEvent($response));
            }
        });
    }

    /**
     * Create an options resolver for the connection parameters
     *
     * @return OptionsResolver
     */
    protected function configureOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'scheme' => 'http',
            'host' => 'localhost',
            'port' => 7474,
            'timeout' => 60
        ]);
        $resolver->setDefined(['username', 'password']);
        $resolver->setRequired(['scheme', 'host', 'port']);
        $resolver->setAllowedTypes('scheme', 'string');
        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('port', ['int', 'null']);
        $resolver->setAllowedTypes('username', 'string');
        $resolver->setAllowedTypes('password', 'string');
        $resolver->setAllowedTypes('timeout', 'int');
        $resolver->setAllowedValues('scheme', ['http', 'https']);
        $resolver->setNormalizer('port', function ($options, $value) {
            if (in_array($value, [80, 0, null], true)) {
                return '';
            }

            if ($value === 443 && $options['scheme'] === 'https') {
                return '';
            }

            return sprintf(':%s', $value);
        });

        return $resolver;
    }

    /**
     * Prepare a statement array to be sent to the Neo4j API
     *
     * @param string $query
     * @param array $parameters
     *
     * @return array
     */
    protected function getStatementArray($query, array $parameters)
    {
        $statement = [
            'statement' => (string) $query,
            'resultDataContents' => ['graph'],
        ];

        if (count($parameters) > 0) {
            $statement['parameters'] = $parameters;
        }

        return $statement;
    }

    /**
     * Send the set of statements to the API
     *
     * @param array $statements
     *
     * @return array
     */
    protected function executeStatements(array $statements)
    {
        $endpoint = sprintf(
            'transaction/%s',
            $this->isTransactionOpened() ?
                $this->transactionId :
                'commit'
        );

        $response = $this->http->post($endpoint, [
            'body' => json_encode(['statements' => $statements]),
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new TransactionException(
                sprintf('Query failed (reason phrase: %s)', $response->getReasonPhrase()),
                TransactionException::QUERY_FAILURE
            );
        }

        $content = $response->json();

        if (count($content['errors']) > 0) {
            throw new TransactionException(
                $content['errors'][0]['message'],
                TransactionException::QUERY_FAILURE
            );
        }

        return $this->getData($content['results']);
    }

    /**
     * Loop over the results to extract properly the data
     *
     * @param array $results
     *
     * @return array
     */
    protected function getData($results)
    {
        $nodes = [];
        $relationships = [];

        foreach ($results as $result) {
            foreach ($result['data'] as $element) {
                foreach ($element['graph']['nodes'] as $node) {
                    $nodes[$node['id']] = $node;
                }
                foreach ($element['graph']['relationships'] as $relationship) {
                    $relationships[$relationship['id']] = $relationship;
                }
            }
        }

        return [
            'nodes' => array_values($nodes),
            'relationships' => array_values($relationships),
        ];
    }
}
