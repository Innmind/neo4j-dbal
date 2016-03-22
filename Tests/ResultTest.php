<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Tests;

use Innmind\Neo4j\DBAL\{
    Result,
    Result\NodeInterface,
    Result\RelationshipInterface,
    Result\RowInterface
};
use Innmind\Immutable\TypedCollectionInterface;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFromRaw()
    {
        $r = Result::fromRaw([]);

        $this->assertInstanceOf(TypedCollectionInterface::class, $r->nodes());
        $this->assertInstanceOf(TypedCollectionInterface::class, $r->relationships());
        $this->assertInstanceOf(TypedCollectionInterface::class, $r->rows());
        $this->assertSame(NodeInterface::class, $r->nodes()->getType());
        $this->assertSame(RelationshipInterface::class, $r->relationships()->getType());
        $this->assertSame(RowInterface::class, $r->rows()->getType());
        $this->assertSame(0, $r->nodes()->count());
        $this->assertSame(0, $r->relationships()->count());
        $this->assertSame(0, $r->rows()->count());

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

        $this->assertSame(2, $r->nodes()->count());
        $this->assertSame(
            ['name' => 'value'],
            $r->rows()->first()->value()
        );
        $this->assertSame('baz', $r->rows()->first()->column());
        $this->assertSame([0], $r->rows()->keys()->toPrimitive());
        $this->assertSame(
            19,
            $r->nodes()->first()->id()->value()
        );
        $this->assertSame([19, 21], $r->nodes()->keys()->toPrimitive());
        $this->assertSame(
            ['Bike'],
            $r->nodes()->first()->labels()->toPrimitive()
        );
        $this->assertSame(
            ['weight' => 10],
            $r->nodes()->first()->properties()->toPrimitive()
        );
        $this->assertSame(2, $r->relationships()->count());
        $this->assertSame(
            9,
            $r->relationships()->first()->id()->value()
        );
        $this->assertSame([9, 10], $r->relationships()->keys()->toPrimitive());
        $this->assertSame(
            'HAS',
            $r->relationships()->first()->type()->value()
        );
        $this->assertSame(
            19,
            $r->relationships()->first()->startNode()->value()
        );
        $this->assertSame(
            20,
            $r->relationships()->first()->endNode()->value()
        );
        $this->assertSame(
            ['position' => 1],
            $r->relationships()->first()->properties()->toPrimitive()
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

        $this->assertSame(10, $r->rows()->count());
        $this->assertSame(6, $r->nodes()->count());
        $this->assertSame(0, $r->relationships()->count());
        $this->assertSame(range(0, 9), $r->rows()->keys()->toPrimitive());
    }
}
