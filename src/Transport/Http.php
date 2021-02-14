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

    public function execute(Query $query): Result
    {
        $response = ($this->fulfill)(
            ($this->translate)($query)
        );

        if (!$this->isSuccessful($response)) {
            throw new QueryFailed($query, $response);
        }

        /** @var array{results: array{0?: array{columns: list<string>, data: list<array{row: list<scalar|array>, graph: array{nodes: list<array{id: numeric, labels: list<string>, properties: array<string, scalar|array>}>, relationships: list<array{id: numeric, type: string, startNode: numeric, endNode: numeric, properties: array<string, scalar|array>}>}}>}}} */
        $response = Json::decode($response->body()->toString());

        return Result\Result::fromRaw($response['results'][0] ?? [
            'columns' => [],
            'data' => [],
        ]);
    }

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
                (int) $e->getCode(),
                $e,
            );
        }

        if ($code->isSuccessful()) {
            return;
        }

        throw new ServerDown;
    }

    private function isSuccessful(Response $response): bool
    {
        if ($response->statusCode()->value() !== 200) {
            return false;
        }

        /** @var array{errors: array} */
        $json = Json::decode($response->body()->toString());

        return \count($json['errors']) === 0;
    }
}
