<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Relationship;

use Innmind\Neo4j\DBAL\{
    Result\Relationship as RelationshipInterface,
    Result\Id,
    Result\Type,
};
use Innmind\Immutable\Map;

final class Relationship implements RelationshipInterface
{
    private Id $id;
    private Type $type;
    private Id $startNode;
    private Id $endNode;
    /** @var Map<string, scalar|array> */
    private Map $properties;

    /**
     * @param Map<string, scalar|array> $properties
     */
    public function __construct(
        Id $id,
        Type $type,
        Id $startNode,
        Id $endNode,
        Map $properties
    ) {
        if (
            (string) $properties->keyType() !== 'string' ||
            (string) $properties->valueType() !== 'scalar|array'
        ) {
            throw new \TypeError('Argument 5 must be of type Map<string, scalar|array>');
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
    public function properties(): Map
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
