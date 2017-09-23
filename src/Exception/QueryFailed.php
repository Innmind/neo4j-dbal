<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Exception;

use Innmind\Neo4j\DBAL\Query;
use Innmind\Http\Message\Response;

final class QueryFailed extends \RuntimeException implements Exception
{
    private $query;
    private $response;

    public function __construct(
        Query $query,
        Response $response
    ) {
        parent::__construct('The query failed to execute properly', 400);
        $this->query = $query;
        $this->response = $response;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function query(): Query
    {
        return $this->query;
    }
}
