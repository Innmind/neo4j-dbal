<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Result,
    Result\NodeInterface,
    Result\RelationshipInterface,
    Result\RowInterface
};
use Innmind\Immutable\{
    MapInterface,
    StreamInterface
};
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testFromRaw()
    {
        $r = Result::fromRaw([]);

        $this->assertInstanceOf(MapInterface::class, $r->nodes());
        $this->assertInstanceOf(MapInterface::class, $r->relationships());
        $this->assertInstanceOf(StreamInterface::class, $r->rows());
        $this->assertSame('int', (string) $r->nodes()->keyType());
        $this->assertSame(NodeInterface::class, (string) $r->nodes()->valueType());
        $this->assertSame('int', (string) $r->relationships()->keyType());
        $this->assertSame(RelationshipInterface::class, (string) $r->relationships()->valueType());
        $this->assertSame(RowInterface::class, (string) $r->rows()->type());
        $this->assertCount(0, $r->nodes());
        $this->assertCount(0, $r->relationships());
        $this->assertCount(0, $r->rows());

        $r = Result::fromRaw([
            'columns' => ['baz'],
            'data' => [[
                'row' => [[
                    'name' => 'value',
                ]],
                'graph' => [
                    'nodes' => [
                        [
                            'id' => '19',
                            'labels' => ['Bike'],
                            'properties' => [
                                'weight' => 10,
                            ],
                        ],
                        [
                            'id' => '21',
                            'labels' => ['Wheel'],
                            'properties' => [
                                'spokes' => 32,
                            ],
                        ],
                    ],
                    'relationships' => [
                        [
                            'id' => '9',
                            'type' => 'HAS',
                            'startNode' => '19',
                            'endNode' => '20',
                            'properties' => [
                                'position' => 1,
                            ],
                        ],
                        [
                            'id' => '10',
                            'type' => 'HAS',
                            'startNode' => '19',
                            'endNode' => '21',
                            'properties' => [
                                'position' => 2,
                            ],
                        ],
                    ],
                ],
            ]],
        ]);

        $this->assertCount(2, $r->nodes());
        $this->assertSame(
            ['name' => 'value'],
            $r->rows()->first()->value()
        );
        $this->assertSame('baz', $r->rows()->first()->column());
        $this->assertSame(
            19,
            $r->nodes()->current()->id()->value()
        );
        $this->assertSame([19, 21], $r->nodes()->keys()->toPrimitive());
        $this->assertSame(
            ['Bike'],
            $r->nodes()->current()->labels()->toPrimitive()
        );
        $this->assertCount(1, $r->relationships()->current()->properties());
        $this->assertSame(
            10,
            $r->nodes()->current()->properties()->get('weight')
        );
        $this->assertCount(2, $r->relationships());
        $this->assertSame(
            9,
            $r->relationships()->current()->id()->value()
        );
        $this->assertSame([9, 10], $r->relationships()->keys()->toPrimitive());
        $this->assertSame(
            'HAS',
            $r->relationships()->current()->type()->value()
        );
        $this->assertSame(
            19,
            $r->relationships()->current()->startNode()->value()
        );
        $this->assertSame(
            20,
            $r->relationships()->current()->endNode()->value()
        );
        $this->assertCount(1, $r->relationships()->current()->properties());
        $this->assertSame(
            1,
            $r->relationships()->current()->properties()->get('position')
        );
    }

    public function testFromRawWithMultipleRows()
    {
        $r = Result::fromRaw([
            'columns' => [
                'entity',
                'n2',
            ],
            'data' => [
                [
                    'row' => [
                        [
                            'uuid' => '31111111-1111-1111-1111-111111111111',
                        ],
                        [],
                    ],
                    'graph' => [
                        'nodes' => [
                            [
                                'id' => '288',
                                'labels' => [
                                    'Bar',
                                ],
                                'properties' => [],
                            ],
                            [
                                'id' => '283',
                                'labels' => [
                                    'Label',
                                ],
                                'properties' => [
                                    'uuid' => '31111111-1111-1111-1111-111111111111',
                                ],
                            ],
                        ],
                        'relationships' => [],
                    ],
                ],
                [
                    'row' => [
                        [
                            'uuid' => '41111111-1111-1111-1111-111111111111',
                        ],
                        [],
                    ],
                    'graph' => [
                        'nodes' => [
                            [
                                'id' => '288',
                                'labels' => [
                                    'Bar',
                                ],
                                'properties' => [],
                            ],
                            [
                                'id' => '284',
                                'labels' => [
                                    'Label',
                                ],
                                'properties' => [
                                    'uuid' => '41111111-1111-1111-1111-111111111111',
                                ],
                            ],
                        ],
                        'relationships' => [],
                    ],
                ],
                [
                    'row' => [
                        [
                            'uuid' => '51111111-1111-1111-1111-111111111111',
                            'content' => 'foo',
                        ],
                        [],
                    ],
                    'graph' => [
                        'nodes' => [
                            [
                                'id' => '288',
                                'labels' => [
                                    'Bar',
                                ],
                                'properties' => [],
                            ],
                            [
                                'id' => '285',
                                'labels' => [
                                    'Label',
                                ],
                                'properties' => [
                                    'uuid' => '51111111-1111-1111-1111-111111111111',
                                    'content' => 'foo',
                                ],
                            ],
                        ],
                        'relationships' => [],
                    ],
                ],
                [
                    'row' => [
                        [
                            'uuid' => '61111111-1111-1111-1111-111111111111',
                            'content' => 'foobar',
                        ],
                        [],
                    ],
                    'graph' => [
                        'nodes' => [
                            [
                                'id' => '288',
                                'labels' => [
                                    'Bar',
                                ],
                                'properties' => [],
                            ],
                            [
                                'id' => '286',
                                'labels' => [
                                    'Label',
                                ],
                                'properties' => [
                                    'uuid' => '61111111-1111-1111-1111-111111111111',
                                    'content' => 'foobar',
                                ],
                            ],
                        ],
                        'relationships' => [],
                    ]
                ],
                [
                    'row' => [
                        [
                            'uuid' => '71111111-1111-1111-1111-111111111111',
                            'content' => 'bar',
                        ],
                        [],
                    ],
                    'graph' => [
                        'nodes' => [
                            [
                                'id' => '288',
                                'labels' => [
                                    'Bar',
                                ],
                                'properties' => [],
                            ],
                            [
                                'id' => '287',
                                'labels' => [
                                    'Label',
                                ],
                                'properties' => [
                                    'uuid' => '71111111-1111-1111-1111-111111111111',
                                    'content' => 'bar',
                                ],
                            ],
                        ],
                        'relationships' => [],
                    ],
                ],
            ],
        ]);

        $this->assertCount(10, $r->rows());
        $this->assertCount(6, $r->nodes());
        $this->assertCount(0, $r->relationships());
    }
}
