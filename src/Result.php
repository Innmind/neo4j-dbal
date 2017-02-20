<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Result\{
    NodeInterface,
    RelationshipInterface,
    RowInterface,
    Node,
    Relationship,
    Row,
    Id,
    Type
};
use Innmind\Immutable\{
    MapInterface,
    StreamInterface,
    Map,
    Stream,
    Set
};

class Result implements ResultInterface
{
    private $nodes;
    private $relationships;
    private $rows;

    public function __construct(
        MapInterface $nodes,
        MapInterface $relationships,
        StreamInterface $rows
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
    public function nodes(): MapInterface
    {
        return $this->nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function relationships(): MapInterface
    {
        return $this->relationships;
    }

    /**
     * {@inheritdoc}
     */
    public function rows(): StreamInterface
    {
        return $this->rows;
    }

    /**
     * @param array $data
     *
     * @return MapInterface<int, Node>
     */
    private static function buildNodes(array $data): MapInterface
    {
        $nodes = new Map('int', NodeInterface::class);

        foreach ($data as $response) {
            foreach ($response['graph']['nodes'] as $node) {
                $labels = new Set('string');

                foreach ($node['labels'] as $label) {
                    $labels = $labels->add($label);
                }

                $nodes = $nodes->put(
                    (int) $node['id'],
                    new Node(
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
     * @return MapInterface<int, Relationship>
     */
    private static function buildRelationships(array $data): MapInterface
    {
        $relationships = new Map('int', RelationshipInterface::class);

        foreach ($data as $response) {
            foreach ($response['graph']['relationships'] as $rel) {
                $relationships = $relationships->put(
                    (int) $rel['id'],
                    new Relationship(
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
     * @return StreamInterface<RowInterface>
     */
    public static function buildRows(array $data): StreamInterface
    {
        $rows = new Stream(RowInterface::class);
        $responses = $data['data'] ?? [];

        foreach ($responses as $response) {
            foreach ($response['row'] as $idx => $row) {
                $rows = $rows->add(new Row(
                    $data['columns'][$idx],
                    $row
                ));
            }
        }

        return $rows;
    }

    /**
     * @return MapInterface<string, variable>
     */
    private static function buildProperties(array $data): MapInterface
    {
        $properties = new Map('string', 'variable');

        foreach ($data as $key => $value) {
            $properties = $properties->put($key, $value);
        }

        return $properties;
    }
}
