<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\HttpTransport\Transport;
use Innmind\TimeContinuum\Clock;
use Innmind\Http\{
    Headers,
    Header,
    Header\ContentType,
    Header\Parameter\Parameter,
    Message\Request\Request,
    Message\Method,
    ProtocolVersion,
};
use Innmind\Stream\Readable\Stream;
use Innmind\Url\Url;
use Innmind\Json\Json;
use Innmind\Immutable\{
    Sequence,
    Map,
};
use function Innmind\Immutable\first;

final class Transactions
{
    private Sequence $transactions;
    private Transport $fulfill;
    private Clock $clock;
    private Headers $headers;
    private Stream $body;

    public function __construct(Transport $fulfill, Clock $clock)
    {
        $this->transactions = Sequence::of(Transaction::class);
        $this->fulfill = $fulfill;
        $this->clock = $clock;
        $this->headers = new Headers(
            ContentType::of(
                'application',
                'json',
                new Parameter('charset', 'UTF-8'),
            ),
        );
        $this->body = Stream::ofContent(Json::encode(['statements' => []]));
    }

    /**
     * Open a new transaction
     *
     * @return Transaction
     */
    public function open(): Transaction
    {
        $response = ($this->fulfill)(
            new Request(
                Url::of('/db/data/transaction'),
                Method::post(),
                new ProtocolVersion(1, 1),
                $this->headers,
                $this->body
            )
        );

        $body = Json::decode($response->body()->toString());
        $location = first($response->headers()->get('Location')->values());
        $transaction = new Transaction(
            Url::of($location->toString()),
            $this->clock->at($body['transaction']['expires']),
            Url::of($body['commit'])
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
     */
    public function commit(): void
    {
        ($this->fulfill)(
            new Request(
                $this->current()->commitEndpoint(),
                Method::post(),
                new ProtocolVersion(1, 1),
                $this->headers,
                $this->body
            )
        );
        $this->transactions = $this->transactions->dropEnd(1);
    }

    /**
     * Rollback the current transaction
     */
    public function rollback(): void
    {
        ($this->fulfill)(
            new Request(
                $this->current()->endpoint(),
                Method::delete(),
                new ProtocolVersion(1, 1)
            )
        );
        $this->transactions = $this->transactions->dropEnd(1);
    }
}
