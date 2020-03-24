<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Result as ResultInterface;
use Innmind\Immutable\{
    Map,
    Sequence,
    Set,
};

final class Result implements ResultInterface
{
    /** @var Map<int, Node> */
    private Map $nodes;
    /** @var Map<int, Relationship> */
    private Map $relationships;
    /** @var Sequence<Row> */
    private Sequence $rows;

    /**
     * @param Map<int, Node> $nodes
     * @param Map<int, Relationship> $relationships
     * @param Sequence<Row> $rows
     */
    public function __construct(Map $nodes, Map $relationships, Sequence $rows)
    {
        $this->nodes = $nodes;
        $this->relationships = $relationships;
        $this->rows = $rows;
    }

    /**
     * Build a result object out of a standard neo4j rest api response
     *
     * @param array{columns: list<string>, data: list<array{row: list<scalar|array>, graph: array{nodes: list<array{id: numeric, labels: list<string>, properties: array<string, scalar|array>}>, relationships: list<array{id: numeric, type: string, startNode: numeric, endNode: numeric, properties: array<string, scalar|array>}>}}>} $response
     */
    public static function fromRaw(array $response): self
    {
        $data = $response['data'] ?? [];

        return new self(
            self::buildNodes($data),
            self::buildRelationships($data),
            self::buildRows($response),
        );
    }

    public function nodes(): Map
    {
        return $this->nodes;
    }

    public function relationships(): Map
    {
        return $this->relationships;
    }

    public function rows(): Sequence
    {
        return $this->rows;
    }

    /**
     * @param list<array{graph: array{nodes: list<array{labels: list<string>, id: numeric, properties: array<string, scalar|array>}>}}> $data
     *
     * @return Map<int, Node>
     */
    private static function buildNodes(array $data): Map
    {
        /** @var Map<int, Node> */
        $nodes = Map::of('int', Node::class);

        foreach ($data as $response) {
            foreach ($response['graph']['nodes'] as $node) {
                $labels = Set::strings(...\array_values($node['labels']));

                $nodes = ($nodes)(
                    (int) $node['id'],
                    new Node\Node(
                        new Id((int) $node['id']),
                        $labels,
                        self::buildProperties($node['properties']),
                    ),
                );
            }
        }

        return $nodes;
    }

    /**
     * @param list<array{graph: array{relationships: list<array{id: numeric, type: string, startNode: numeric, endNode: numeric, properties: array<string, scalar|array>}>}}> $data
     *
     * @return Map<int, Relationship>
     */
    private static function buildRelationships(array $data): Map
    {
        /** @var Map<int, Relationship> */
        $relationships = Map::of('int', Relationship::class);

        foreach ($data as $response) {
            foreach ($response['graph']['relationships'] as $rel) {
                $relationships = ($relationships)(
                    (int) $rel['id'],
                    new Relationship\Relationship(
                        new Id((int) $rel['id']),
                        new Type($rel['type']),
                        new Id((int) $rel['startNode']),
                        new Id((int) $rel['endNode']),
                        self::buildProperties($rel['properties']),
                    ),
                );
            }
        }

        return $relationships;
    }

    /**
     * @param array{data?: list<array{row: array<int, scalar|array>}>, columns: array<int, string>} $data
     *
     * @return Sequence<Row>
     */
    private static function buildRows(array $data): Sequence
    {
        /** @var Sequence<Row> */
        $rows = Sequence::of(Row::class);
        $responses = $data['data'] ?? [];

        foreach ($responses as $response) {
            foreach ($response['row'] as $idx => $row) {
                $rows = ($rows)(new Row\Row(
                    $data['columns'][$idx],
                    $row,
                ));
            }
        }

        return $rows;
    }

    /**
     * @param array<string, scalar|array> $data
     *
     * @return Map<string, scalar|array>
     */
    private static function buildProperties(array $data): Map
    {
        /** @var Map<string, scalar|array> */
        $properties = Map::of('string', 'scalar|array');

        foreach ($data as $key => $value) {
            $properties = ($properties)($key, $value);
        }

        return $properties;
    }
}
