<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    QueryInterface,
    Transactions
};
use Innmind\Http\{
    Headers,
    Header\HeaderInterface,
    Header\ContentType,
    Header\ContentTypeValue,
    Header\Accept,
    Header\AcceptValue,
    Header\HeaderValueInterface,
    Header\ParameterInterface,
    Header\Parameter,
    Message\Request,
    Message\RequestInterface,
    Message\Method,
    ProtocolVersion
};
use Innmind\Filesystem\{
    StreamInterface,
    Stream\StringStream
};
use Innmind\Url\{
    UrlInterface,
    Url
};
use Innmind\Immutable\{
    Map,
    Set
};

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
        $this->headers = new Headers(
            (new Map('string', HeaderInterface::class))
                ->put(
                    'content-type',
                    new ContentType(
                        new ContentTypeValue(
                            'application',
                            'json'
                        )
                    )
                )
                ->put(
                    'accept',
                    new Accept(
                        (new Set(HeaderValueInterface::class))
                            ->add(
                                new AcceptValue(
                                    'application',
                                    'json',
                                    (new Map('string', ParameterInterface::class))
                                        ->put(
                                            'charset',
                                            new Parameter('charset', 'UTF-8')
                                        )
                                )
                            )
                    )
                )
        );
    }

    /**
     * Transalate a dbal query into a http request
     *
     * @param QueryInterface $query
     *
     * @return RequestInterface
     */
    public function translate(QueryInterface $query): RequestInterface
    {
        return new Request(
            $this->computeEndpoint(),
            new Method(Method::POST),
            new ProtocolVersion(1, 1),
            $this->headers,
            $this->computeBody($query)
        );
    }

    /**
     * Determine the appropriate endpoint based on the transactions
     *
     * @return UrlInterface
     */
    private function computeEndpoint(): UrlInterface
    {
        if (!$this->transactions->has()) {
            return Url::fromString('/db/data/transaction/commit');
        }

        return $this->transactions->get()->endpoint();
    }

    /**
     * Build the json payload to be sent to the server
     *
     * @param QueryInterface $query
     *
     * @return StreamInterface
     */
    private function computeBody(QueryInterface $query): StreamInterface
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

        return new StringStream(json_encode([
            'statements' => [$statement],
        ]));
    }
}
