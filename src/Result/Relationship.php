<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Neo4j\DBAL\Exception\InvalidArgumentException;
use Innmind\Immutable\MapInterface;

final class Relationship implements RelationshipInterface
{
    private $id;
    private $type;
    private $startNode;
    private $endNode;
    private $properties;

    public function __construct(
        Id $id,
        Type $type,
        Id $startNode,
        Id $endNode,
        MapInterface $properties
    ) {
        if (
            (string) $properties->keyType() !== 'string' ||
            (string) $properties->valueType() !== 'variable'
        ) {
            throw new InvalidArgumentException;
        }

        $this->id = $id;
        $this->type = $type;
        $this->startNode = $startNode;
        $this->endNode = $endNode;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function id(): Id
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): Type
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function startNode(): Id
    {
        return $this->startNode;
    }

    /**
     * {@inheritdoc}
     */
    public function endNode(): Id
    {
        return $this->endNode;
    }

    /**
     * {@inheritdoc}
     */
    public function properties(): MapInterface
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperties(): bool
    {
        return $this->properties->count() > 0;
    }
}
