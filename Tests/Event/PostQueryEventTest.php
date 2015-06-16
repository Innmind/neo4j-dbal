<?php

namespace Innmind\Neo4j\DBAL\Tests\Event;

use Innmind\Neo4j\DBAL\Event\PostQueryEvent;
use GuzzleHttp\Message\Response;

class PostQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStatements()
    {
        $e = new PostQueryEvent(['foo' => 'bar'], [], new Response(200));

        $this->assertSame(
            ['foo' => 'bar'],
            $e->getStatements()
        );
    }

    public function testGetContent()
    {
        $e = new PostQueryEvent([], ['foo' => 'bar'], new Response(200));

        $this->assertSame(
            ['foo' => 'bar'],
            $e->getContent()
        );
    }

    public function testGetResponse()
    {
        $r = new Response(200);
        $e = new PostQueryEvent([], ['foo' => 'bar'], $r);

        $this->assertSame(
            $r,
            $e->getResponse()
        );
    }
}
