<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Exception;

use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Http\Message\ResponseInterface;

class QueryFailedException extends \RuntimeException implements ExceptionInterface
{
    private $query;
    private $response;

    public function __construct(
        QueryInterface $query,
        ResponseInterface $response
    ) {
        parent::__construct('The query failed to execute properly', 400);
        $this->query = $query;
        $this->response = $response;
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function query(): QueryInterface
    {
        return $this->query;
    }
}
