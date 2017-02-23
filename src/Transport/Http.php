<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    TransportInterface,
    QueryInterface,
    ResultInterface,
    Result,
    Server,
    Authentication,
    Translator\HttpTranslator,
    HttpTransport\Transport,
    Exception\ServerDownException,
    Exception\QueryFailedException
};
use Innmind\Http\{
    Message\ResponseInterface,
    Message\Request,
    Message\Method,
    ProtocolVersion
};
use Innmind\Url\Url;

final class Http implements TransportInterface
{
    private $translator;
    private $transport;

    public function __construct(
        HttpTranslator $translator,
        Transport $transport
    ) {
        $this->translator = $translator;
        $this->transport = $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(QueryInterface $query): ResultInterface
    {
        $response = $this->transport->fulfill(
            $this->translator->translate($query)
        );

        if (!$this->isSuccessful($response)) {
            throw new QueryFailedException($query, $response);
        }

        $response = json_decode((string) $response->body(), true);
        $result = Result::fromRaw($response['results'][0] ?? []);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ping(): TransportInterface
    {
        try {
            $code = $this
                ->transport
                ->fulfill(
                    new Request(
                        Url::fromString('/'),
                        new Method(Method::OPTIONS),
                        new ProtocolVersion(1, 1)
                    )
                )
                ->statusCode()
                ->value();
        } catch (\Exception $e) {
            throw new ServerDownException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        if ($code >= 200 && $code < 300) {
            return $this;
        }

        throw new ServerDownException;
    }

    /**
     * Check if the response is successful
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function isSuccessful(ResponseInterface $response): bool
    {
        if ($response->statusCode()->value() !== 200) {
            return false;
        }

        $json = json_decode((string) $response->body(), true);

        return count($json['errors']) === 0;
    }
}
