<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Result;

use Innmind\Immutable\CollectionInterface;

class Node implements NodeInterface
{
    private $id;
    private $labels;
    private $properties;

    public function __construct(
        Id $id,
        CollectionInterface $labels,
        CollectionInterface $properties
    ) {
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
    public function labels(): CollectionInterface
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
