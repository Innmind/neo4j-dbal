<?php
declare(strict_types = 1);

namespace Tests\Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\{
    Result\Result,
    Result as ResultInterface,
    Result\Node,
    Result\Relationship,
    Result\Row,
};
use Innmind\Immutable\{
    Map,
    Sequence,
};
use function Innmind\Immutable\{
    unwrap,
    first,
};
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testFromRaw()
    {
        $result = Result::fromRaw([]);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertInstanceOf(Map::class, $result->nodes());
        $this->assertInstanceOf(Map::class, $result->relationships());
        $this->assertInstanceOf(Sequence::class, $result->rows());
        $this->assertSame('int', (string) $result->nodes()->keyType());
        $this->assertSame(Node::class, (string) $result->nodes()->valueType());
        $this->assertSame('int', (string) $result->relationships()->keyType());
        $this->assertSame(Relationship::class, (string) $result->relationships()->valueType());
        $this->assertSame(Row::class, (string) $result->rows()->type());
        $this->assertCount(0, $result->nodes());
        $this->assertCount(0, $result->relationships());
        $this->assertCount(0, $result->rows());

        $result = Result::fromRaw([
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

        $this->assertCount(2, $result->nodes());
        $this->assertSame(
            ['name' => 'value'],
            $result->rows()->first()->value()
        );
        $this->assertSame('baz', $result->rows()->first()->column());
        $this->assertSame(
            19,
            $result->nodes()->values()->first()->id()->value()
        );
        $this->assertSame([19, 21], unwrap($result->nodes()->keys()));
        $this->assertSame(
            ['Bike'],
            unwrap($result->nodes()->values()->first()->labels())
        );
        $this->assertCount(1, $result->relationships()->values()->first()->properties());
        $this->assertSame(
            10,
            $result->nodes()->values()->first()->properties()->get('weight')
        );
        $this->assertCount(2, $result->relationships());
        $this->assertSame(
            9,
            $result->relationships()->values()->first()->id()->value()
        );
        $this->assertSame([9, 10], unwrap($result->relationships()->keys()));
        $this->assertSame(
            'HAS',
            $result->relationships()->values()->first()->type()->value()
        );
        $this->assertSame(
            19,
            $result->relationships()->values()->first()->startNode()->value()
        );
        $this->assertSame(
            20,
            $result->relationships()->values()->first()->endNode()->value()
        );
        $this->assertCount(1, $result->relationships()->values()->first()->properties());
        $this->assertSame(
            1,
            $result->relationships()->values()->first()->properties()->get('position')
        );
    }

    public function testFromRawWithMultipleRows()
    {
        $result = Result::fromRaw([
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

        $this->assertCount(10, $result->rows());
        $this->assertCount(6, $result->nodes());
        $this->assertCount(0, $result->relationships());
    }
}
