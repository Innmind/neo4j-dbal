<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    Query,
    Transactions,
};
use Innmind\Http\{
    Headers,
    Header,
    Header\ContentType,
    Header\Accept,
    Header\AcceptValue,
    Header\Value,
    Header\Parameter\Parameter,
    Message\Request,
    Message\Method,
    ProtocolVersion,
};
use Innmind\Stream\Readable\Stream;
use Innmind\Url\Url;
use Innmind\Json\Json;
use Innmind\Immutable\Map;

/**
 * Translate a dbal query into a http request
 */
final class HttpTranslator
{
    private Transactions $transactions;
    private Headers $headers;

    public function __construct(Transactions $transactions)
    {
        $this->transactions = $transactions;
        /** @psalm-suppress InvalidArgument */
        $this->headers = Headers::of(
            ContentType::of(
                'application',
                'json',
            ),
            new Accept(
                new AcceptValue(
                    'application',
                    'json',
                    new Parameter('charset', 'UTF-8'),
                ),
            ),
        );
    }

    /**
     * Transalate a dbal query into a http request
     */
    public function __invoke(Query $query): Request
    {
        return new Request\Request(
            $this->computeEndpoint(),
            Method::post(),
            new ProtocolVersion(1, 1),
            $this->headers,
            $this->computeBody($query),
        );
    }

    /**
     * Determine the appropriate endpoint based on the transactions
     */
    private function computeEndpoint(): Url
    {
        if (!$this->transactions->isOpened()) {
            return Url::of('/db/data/transaction/commit');
        }

        return $this->transactions->current()->endpoint();
    }

    /**
     * Build the json payload to be sent to the server
     */
    private function computeBody(Query $query): Stream
    {
        $statement = [
            'statement' => $query->cypher(),
            'resultDataContents' => ['graph', 'row'],
        ];

        if ($query->hasParameters()) {
            $statement['parameters'] = $query->parameters()->values()->reduce(
                [],
                static function(array $parameters, Query\Parameter $parameter): array {
                    /** @psalm-suppress MixedAssignment */
                    $parameters[$parameter->key()] = $parameter->value();

                    return $parameters;
                },
            );
        }

        return Stream::ofContent(Json::encode([
            'statements' => [$statement],
        ]));
    }
}
