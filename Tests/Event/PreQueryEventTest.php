<?php

namespace Innmind\Neo4j\DBAL\Tests\Event;

use Innmind\Neo4j\DBAL\Event\PreQueryEvent;

class PreQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testSetStatements()
    {
        $e = new PreQueryEvent(['foo' => 'bar']);

        $this->assertSame(
            ['foo' => 'bar'],
            $e->getStatements()
        );
        $this->assertSame(
            $e,
            $e->setStatements(['bar' => 'baz'])
        );
        $this->assertSame(
            ['bar' => 'baz'],
            $e->getStatements()
        );
    }
}
