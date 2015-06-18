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
            $e->setStatements([
                [
                    'statement' => 'foo',
                    'resultDataContents' => ['graph', 'row']
                ]
            ])
        );
        $this->assertSame(
            [
                [
                    'statement' => 'foo',
                    'resultDataContents' => ['graph', 'row']
                ]
            ],
            $e->getStatements()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A statement must have the key "resultDataContents" set to "['graph', 'row']"
     */
    public function testThrowIfSettingStatementWithoutResultDataContents()
    {
        $e = new PreQueryEvent([]);

        $e->setStatements([
            [
                'statement' => 'MATCH (a) RETURN a;',
            ],
        ]);
    }
}
