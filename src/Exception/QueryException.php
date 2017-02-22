<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Exception;

use Innmind\Neo4j\DBAL\QueryInterface;
use Innmind\Http\Message\ResponseInterface;

class QueryException extends \RuntimeException implements ExceptionInterface
{
    private $query;
    private $response;

    public static function failed(
        QueryInterface $query,
        ResponseInterface $response
    ): self {
        $exception = new self('The query failed to execute properly', 400);
        $exception->query = $query;
        $exception->response = $response;

        return $exception;
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
