<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Result\NodeInterface;
use Innmind\Neo4j\DBAL\Result\RelationshipInterface;
use Innmind\Neo4j\DBAL\Result\RowInterface;
use Innmind\Neo4j\DBAL\Result\Node;
use Innmind\Neo4j\DBAL\Result\Relationship;
use Innmind\Neo4j\DBAL\Result\Row;
use Innmind\Neo4j\DBAL\Result\Id;
use Innmind\Neo4j\DBAL\Result\Type;
use Innmind\Immutable\TypedCollectionInterface;
use Innmind\Immutable\TypedCollection;
use Innmind\Immutable\Collection;

class Result implements ResultInterface
{
    private $nodes;
    private $relationships;
    private $rows;

    public function __construct(
        TypedCollectionInterface $nodes,
        TypedCollectionInterface $relationships,
        TypedCollectionInterface $rows
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
        $response = $response['data'] ?? [];

        return new self(
            new TypedCollection(
                NodeInterface::class,
                self::buildNodes($response)
            ),
            new TypedCollection(
                RelationshipInterface::class,
                self::buildRelationships($response)
            ),
            new TypedCollection(
                RowInterface::class,
                self::buildRows($response)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function nodes(): TypedCollectionInterface
    {
        return $this->nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function relationships(): TypedCollectionInterface
    {
        return $this->relationships;
    }

    /**
     * {@inheritdoc}
     */
    public function rows(): TypedCollectionInterface
    {
        return $this->rows;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function buildNodes(array $data): array
    {
        $nodes = [];

        foreach ($data as $response) {
            foreach ($response['graph']['nodes'] as $node) {
                $nodes[] = new Node(
                    new Id((int) $node['id']),
                    new Collection($node['labels']),
                    new Collection($node['properties'])
                );
            }
        }

        return $nodes;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function buildRelationships(array $data): array
    {
        $relationships = [];

        foreach ($data as $response) {
            foreach ($response['graph']['relationships'] as $rel) {
                $relationships[] = new Relationship(
                    new Id((int) $rel['id']),
                    new Type($rel['type']),
                    new Id((int) $rel['startNode']),
                    new Id((int) $rel['endNode']),
                    new Collection($rel['properties'])
                );
            }
        }

        return $relationships;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function buildRows(array $data): array
    {
        $rows = [];

        foreach ($data as $response) {
            foreach ($response['row'] as $row) {
                $rows[] = new Row($row);
            }
        }

        return $rows;
    }
}
