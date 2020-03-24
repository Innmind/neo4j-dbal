<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    Transport,
    Query,
    Result,
    Translator\HttpTranslator,
    HttpTransport\Transport as HttpTransport,
    Exception\ServerDown,
    Exception\QueryFailed,
};
use Innmind\Http\{
    Message\Response,
    Message\Request\Request,
    Message\Method,
    ProtocolVersion,
};
use Innmind\Url\Url;
use Innmind\Json\Json;

final class Http implements Transport
{
    private HttpTranslator $translate;
    private HttpTransport $fulfill;

    public function __construct(
        HttpTranslator $translate,
        HttpTransport $fulfill
    ) {
        $this->translate = $translate;
        $this->fulfill = $fulfill;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Query $query): Result
    {
        $response = ($this->fulfill)(
            ($this->translate)($query)
        );

        if (!$this->isSuccessful($response)) {
            throw new QueryFailed($query, $response);
        }

        $response = Json::decode($response->body()->toString());
        $result = Result\Result::fromRaw($response['results'][0] ?? []);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ping(): void
    {
        try {
            $code = ($this->fulfill)
                (
                    new Request(
                        Url::of('/'),
                        Method::options(),
                        new ProtocolVersion(1, 1),
                    ),
                )
                ->statusCode();
        } catch (\Exception $e) {
            throw new ServerDown(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        if ($code->isSuccessful()) {
            return;
        }

        throw new ServerDown;
    }

    /**
     * Check if the response is successful
     *
     * @param Response $response
     *
     * @return bool
     */
    private function isSuccessful(Response $response): bool
    {
        if ($response->statusCode()->value() !== 200) {
            return false;
        }

        $json = Json::decode($response->body()->toString());

        return count($json['errors']) === 0;
    }
}
