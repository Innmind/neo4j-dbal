<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Relationship;

use Innmind\Neo4j\DBAL\{
    Result\Relationship as RelationshipInterface,
    Result\Id,
    Result\Type,
};
use Innmind\Immutable\Map;
use function Innmind\Immutable\assertMap;

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
        assertMap('string', 'scalar|array', $properties, 5);

        $this->id = $id;
        $this->type = $type;
        $this->startNode = $startNode;
        $this->endNode = $endNode;
        $this->properties = $properties;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function startNode(): Id
    {
        return $this->startNode;
    }

    public function endNode(): Id
    {
        return $this->endNode;
    }

    public function properties(): Map
    {
        return $this->properties;
    }

    public function hasProperties(): bool
    {
        return !$this->properties->empty();
    }
}
