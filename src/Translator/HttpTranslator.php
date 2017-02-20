<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Translator;

use Innmind\Neo4j\DBAL\{
    QueryInterface,
    Transactions
};
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Translate a dbal query into a http request
 */
final class HttpTranslator
{
    private $transactions;

    public function __construct(Transactions $transactions)
    {
        $this->transactions = $transactions;
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
            'POST',
            $this->computeEndpoint(),
            [
                'Accept' => 'application/json; charset=UTF-8',
                'Content-Type' => 'application/json',
            ],
            $this->computeBody($query)
        );
    }

    /**
     * Determine the appropriate endpoint based on the transactions
     *
     * @return string
     */
    private function computeEndpoint(): string
    {
        if (!$this->transactions->has()) {
            return '/db/data/transaction/commit';
        }

        return $this->transactions->get()->endpoint();
    }

    /**
     * Build the json payload to be sent to the server
     *
     * @param QueryInterface $query
     *
     * @return string
     */
    private function computeBody(QueryInterface $query): string
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

        return json_encode([
            'statements' => [$statement],
        ]);
    }
}
