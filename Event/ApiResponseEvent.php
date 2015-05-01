<?php

namespace Innmind\Neo4j\DBAL\Event;

use Symfony\Component\EventDispatcher\Event;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Event fired after each request is sent to the Neo4j API
 */
class ApiResponseEvent extends Event
{
    protected $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Return the guzzle http response object
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
