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
use function Innmind\Immutable\{
    assertSet,
    assertMap,
};

final class Node implements NodeInterface
{
    private Id $id;
    /** @var Set<string> */
    private Set $labels;
    /** @var Map<string, scalar|array> */
    private Map $properties;

    /**
     * @param Set<string> $labels
     * @param Map<string, scalar|array> $properties
     */
    public function __construct(Id $id, Set $labels, Map $properties)
    {
        assertSet('string', $labels, 2);
        assertMap('string', 'scalar|array', $properties, 3);

        $this->id = $id;
        $this->labels = $labels;
        $this->properties = $properties;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function labels(): Set
    {
        return $this->labels;
    }

    public function hasLabels(): bool
    {
        return !$this->labels->empty();
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
