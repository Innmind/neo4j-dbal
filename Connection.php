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
    protected $transactionId;

    public function __construct(array $params, EventDispatcherInterface $dispatcher)
    {
        $resolver = $this->configureOptions();
        $params = $resolver->resolve($params);

        $this->dispatcher = $dispatcher;

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

    }

    /**
     * @inheritdoc
     */
    public function execute($query)
    {

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

        if ($respose->getStatusCode() !== 200) {
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
            $this->dispatcher->dispatch(Events::API_RESPONSE, new ApiResponseEvent($event->getResponse()));
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
}
