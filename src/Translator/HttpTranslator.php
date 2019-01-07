<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    Query,
    Transactions,
};
use Innmind\Http\{
    Headers\Headers,
    Header,
    Header\ContentType,
    Header\ContentTypeValue,
    Header\Accept,
    Header\AcceptValue,
    Header\Value,
    Header\Parameter,
    Message\Request,
    Message\Method\Method,
    ProtocolVersion\ProtocolVersion,
};
use Innmind\Filesystem\Stream\StringStream;
use Innmind\Url\{
    UrlInterface,
    Url,
};
use Innmind\Json\Json;
use Innmind\Immutable\Map;

/**
 * Translate a dbal query into a http request
 */
final class HttpTranslator
{
    private $transactions;
    private $headers;

    public function __construct(Transactions $transactions)
    {
        $this->transactions = $transactions;
        $this->headers = Headers::of(
            new ContentType(
                new ContentTypeValue(
                    'application',
                    'json'
                )
            ),
            new Accept(
                new AcceptValue(
                    'application',
                    'json',
                    Map::of('string', Parameter::class)
                        ('charset', new Parameter\Parameter('charset', 'UTF-8'))
                )
            )
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
            $this->computeBody($query)
        );
    }

    /**
     * Determine the appropriate endpoint based on the transactions
     */
    private function computeEndpoint(): UrlInterface
    {
        if (!$this->transactions->isOpened()) {
            return Url::fromString('/db/data/transaction/commit');
        }

        return $this->transactions->current()->endpoint();
    }

    /**
     * Build the json payload to be sent to the server
     */
    private function computeBody(Query $query): StringStream
    {
        $statement = [
            'statement' => $query->cypher(),
            'resultDataContents' => ['graph', 'row'],
        ];

        if ($query->hasParameters()) {
            $parameters = [];

            foreach ($query->parameters() as $parameter) {
                $parameters[$parameter->key()] = $parameter->value();
            }

            $statement['parameters'] = $parameters;
        }

        return new StringStream(Json::encode([
            'statements' => [$statement],
        ]));
    }
}
