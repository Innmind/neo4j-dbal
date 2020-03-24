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
    private Map $nodes;
    private Map $relationships;
    private Sequence $rows;

    public function __construct(
        Map $nodes,
        Map $relationships,
        Sequence $rows
    ) {
        $this->nodes = $nodes;
        $this->relationships = $relationships;
        $this->rows = $rows;
    }

    /**
     * Build a result object out of a standard neo4j rest api response
     *
     * @param array $response
     *
     * @return self
     */
    public static function fromRaw(array $response): self
    {
        $data = $response['data'] ?? [];

        return new self(
            self::buildNodes($data),
            self::buildRelationships($data),
            self::buildRows($response)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function nodes(): Map
    {
        return $this->nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function relationships(): Map
    {
        return $this->relationships;
    }

    /**
     * {@inheritdoc}
     */
    public function rows(): Sequence
    {
        return $this->rows;
    }

    /**
     * @param array $data
     *
     * @return Map<int, Node>
     */
    private static function buildNodes(array $data): Map
    {
        $nodes = Map::of('int', Node::class);

        foreach ($data as $response) {
            foreach ($response['graph']['nodes'] as $node) {
                $labels = Set::of('string', ...\array_values($node['labels']));

                $nodes = $nodes->put(
                    (int) $node['id'],
                    new Node\Node(
                        new Id((int) $node['id']),
                        $labels,
                        self::buildProperties($node['properties'])
                    )
                );
            }
        }

        return $nodes;
    }

    /**
     * @param array $data
     *
     * @return Map<int, Relationship>
     */
    private static function buildRelationships(array $data): Map
    {
        $relationships = Map::of('int', Relationship::class);

        foreach ($data as $response) {
            foreach ($response['graph']['relationships'] as $rel) {
                $relationships = $relationships->put(
                    (int) $rel['id'],
                    new Relationship\Relationship(
                        new Id((int) $rel['id']),
                        new Type($rel['type']),
                        new Id((int) $rel['startNode']),
                        new Id((int) $rel['endNode']),
                        self::buildProperties($rel['properties'])
                    )
                );
            }
        }

        return $relationships;
    }

    /**
     * @param array $data
     *
     * @return Sequence<Row>
     */
    private static function buildRows(array $data): Sequence
    {
        $rows = Sequence::of(Row::class);
        $responses = $data['data'] ?? [];

        foreach ($responses as $response) {
            foreach ($response['row'] as $idx => $row) {
                $rows = $rows->add(new Row\Row(
                    $data['columns'][$idx],
                    $row
                ));
            }
        }

        return $rows;
    }

    /**
     * @return Map<string, variable>
     */
    private static function buildProperties(array $data): Map
    {
        $properties = Map::of('string', 'variable');

        foreach ($data as $key => $value) {
            $properties = ($properties)($key, $value);
        }

        return $properties;
    }
}
