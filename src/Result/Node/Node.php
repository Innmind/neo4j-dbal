<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result\Node;

use Innmind\Neo4j\DBAL\{
    Result\Node as NodeInterface,
    Result\Id,
};
use Innmind\Immutable\{
    Set,
    Map,
};

final class Node implements NodeInterface
{
    private Id $id;
    private Set $labels;
    private Map $properties;

    public function __construct(
        Id $id,
        Set $labels,
        Map $properties
    ) {
        if ((string) $labels->type() !== 'string') {
            throw new \TypeError('Argument 2 must be of type Set<string>');
        }

        if (
            (string) $properties->keyType() !== 'string' ||
            (string) $properties->valueType() !== 'variable'
        ) {
            throw new \TypeError('Argument 3 must be of type Map<string, variable>');
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
    public function labels(): Set
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
