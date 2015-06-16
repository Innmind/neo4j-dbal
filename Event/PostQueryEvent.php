<?php

namespace Innmind\Neo4j\DBAL\Event;

use Symfony\Component\EventDispatcher\Event;
use GuzzleHttp\Message\ResponseInterface;

class PostQueryEvent extends Event
{
    protected $statements;
    protected $content;
    protected $response;

    public function __construct(array $statements, $content, ResponseInterface $response)
    {
        $this->statements = $statements;
        $this->content = $content;
        $this->response = $response;
    }

    /**
     * Return the statements to be executed
     *
     * @return array
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Return the decoded json response
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return the response object
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
