<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\CollectionInterface;

class Relationship implements RelationshipInterface
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
        CollectionInterface $properties
    ) {
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
    public function properties(): CollectionInterface
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
