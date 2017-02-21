<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Immutable\Stream;
use GuzzleHttp\Client;

final class Transactions
{
    private $transactions;
    private $http;
    private $clock;

    public function __construct(
        Server $server,
        Authentication $authentication,
        TimeContinuumInterface $clock,
        int $timeout = 60
    ) {
        $this->transactions = new Stream(Transaction::class);
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
        $this->clock = $clock;
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
            $this->clock->at($body['transaction']['expires']),
            $body['commit']
        );

        $this->transactions = $this->transactions->add($transaction);

        return $transaction;
    }

    /**
     * Check if a transaction is opened
     *
     * @return bool
     */
    public function has(): bool
    {
        return $this->transactions->size() > 0;
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
        $this->transactions = $this->transactions->dropEnd(1);

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
        $this->transactions = $this->transactions->dropEnd(1);

        return $this;
    }
}
