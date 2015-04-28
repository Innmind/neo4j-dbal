<?php

namespace Innmind\Neo4j\DBAL\Tests\EventListener;

use Innmind\Neo4j\DBAL\EventListener\ResponseListener;
use Innmind\Neo4j\DBAL\Event\ApiResponseEvent;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidCredsException()
    {
        $listener = new ResponseListener();
        $response = new Response(401);
        $event = new ApiResponseEvent($response);

        $listener->handle($event);
    }

    /**
     * @expectedException Innmind\Neo4j\DBAL\Exception\PasswordChangeRequiredException
     */
    public function testPasswordChangeException()
    {
        $listener = new ResponseListener();
        $stream = Stream::factory(json_encode([
            'username' => 'neo4j',
            'password_change' => 'http://localhost:7474/user/neo4j/password',
            'password_change_required' => true,
        ]));
        $response = new Response(200, [], $stream);
        $event = new ApiResponseEvent($response);

        $listener->handle($event);
    }
}
