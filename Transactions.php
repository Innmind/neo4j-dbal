<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Immutable\TypedCollection;
use GuzzleHttp\Client;

class Transactions
{
    private $transactions;
    private $http;

    public function __construct(
        Server $server,
        Authentication $authentication,
        int $timeout = 60
    ) {
        $this->transactions = new TypedCollection(
            Transaction::class,
            []
        );
        $this->http = new Client([
            'base_uri' => (string) $server,
            'timeout' => $timeout,
            'headers' => [
                'Authorization' => base64_encode(sprintf(
                    '%s:%s',
                    $authentication->user(),
                    $authentication->password()
                )),
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Open a new transaction
     *
     * @return Transaction
     */
    public function open(): Transaction
    {
        $response = $this->http->post(
            '/db/data/transaction',
            [
                'json' => ['statements' => []],
                'headers' => [
                    'Content-Type' => 'application/json; charset=UTF-8',
                ],
            ]
        );
        $body = json_decode((string) $response->getBody(), true);
        $transaction = new Transaction(
            $response->getHeaderLine('Location'),
            $body['transaction']['expires'],
            $body['commit']
        );

        $this->transactions = $this->transactions->push($transaction);

        return $transaction;
    }

    /**
     * Check if a transaction is opened
     *
     * @return bool
     */
    public function has(): bool
    {
        return $this->transactions->count() > 0;
    }

    /**
     * Return the current transaction
     *
     * @return Transaction
     */
    public function get(): Transaction
    {
        return $this->transactions->last();
    }

    /**
     * Commit the current transaction
     *
     * @return self
     */
    public function commit(): self
    {
        $transaction = $this->get();
        $this->http->post(
            $transaction->commitEndpoint(),
            [
                'json' => ['statements' => []],
                'headers' => [
                    'Content-Type' => 'application/json; charset=UTF-8',
                ],
            ]
        );
        $this->transactions = $this->transactions->pop();

        return $this;
    }

    /**
     * Rollback the current transaction
     *
     * @return self
     */
    public function rollback(): self
    {
        $transaction = $this->get();
        $this->http->delete($transaction->endpoint());
        $this->transactions = $this->transactions->pop();

        return $this;
    }
}
