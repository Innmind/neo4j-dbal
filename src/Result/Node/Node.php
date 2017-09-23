<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Node;

use Innmind\Neo4j\DBAL\{
    Result\Node as NodeInterface,
    Result\Id,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\{
    SetInterface,
    MapInterface
};

final class Node implements NodeInterface
{
    private $id;
    private $labels;
    private $properties;

    public function __construct(
        Id $id,
        SetInterface $labels,
        MapInterface $properties
    ) {
        if (
            (string) $labels->type() !== 'string' ||
            (string) $properties->keyType() !== 'string' ||
            (string) $properties->valueType() !== 'variable'
        ) {
            throw new InvalidArgumentException;
        }

        $this->id = $id;
        $this->labels = $labels;
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
    public function labels(): SetInterface
    {
        return $this->labels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLabels(): bool
    {
        return $this->labels->count() > 0;
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
