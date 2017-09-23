<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\HttpTransport\Transport;
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Http\{
    Headers\Headers,
    Header,
    Header\ContentType,
    Header\ContentTypeValue,
    Header\Parameter,
    Message\Request\Request,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion
};
use Innmind\Filesystem\Stream\StringStream;
use Innmind\Url\Url;
use Innmind\Immutable\{
    Stream,
    Map
};

final class Transactions
{
    private $transactions;
    private $transport;
    private $clock;
    private $headers;
    private $body;

    public function __construct(
        Transport $transport,
        TimeContinuumInterface $clock
    ) {
        $this->transactions = new Stream(Transaction::class);
        $this->transport = $transport;
        $this->clock = $clock;
        $this->headers = new Headers(
            (new Map('string', Header::class))
                ->put(
                    'content-type',
                    new ContentType(
                        new ContentTypeValue(
                            'application',
                            'json',
                            (new Map('string', Parameter::class))
                                ->put(
                                    'charset',
                                    new Parameter\Parameter('charset', 'UTF-8')
                                )
                        )
                    )
                )
        );
        $this->body = new StringStream(json_encode(['statements' => []]));
    }

    /**
     * Open a new transaction
     *
     * @return Transaction
     */
    public function open(): Transaction
    {
        $response = $this->transport->fulfill(
            new Request(
                Url::fromString('/db/data/transaction'),
                new Method(Method::POST),
                new ProtocolVersion(1, 1),
                $this->headers,
                $this->body
            )
        );

        $body = json_decode((string) $response->body(), true);
        $location = (string) $response
            ->headers()
            ->get('Location')
            ->values()
            ->current();
        $transaction = new Transaction(
            Url::fromString($location),
            $this->clock->at($body['transaction']['expires']),
            Url::fromString($body['commit'])
        );

        $this->transactions = $this->transactions->add($transaction);

        return $transaction;
    }

    /**
     * Check if a transaction is opened
     *
     * @return bool
     */
    public function isOpened(): bool
    {
        return $this->transactions->size() > 0;
    }

    /**
     * Return the current transaction
     *
     * @return Transaction
     */
    public function current(): Transaction
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
        $this->transport->fulfill(
            new Request(
                $this->current()->commitEndpoint(),
                new Method(Method::POST),
                new ProtocolVersion(1, 1),
                $this->headers,
                $this->body
            )
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
        $this->transport->fulfill(
            new Request(
                $this->current()->endpoint(),
                new Method(Method::DELETE),
                new ProtocolVersion(1, 1)
            )
        );
        $this->transactions = $this->transactions->dropEnd(1);

        return $this;
    }
}
